<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSizingOperationRequest;
use App\Http\Requests\UpdateSizingOperationRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\MachineNumberResource;
use App\Http\Resources\MachineTypeResource;
use App\Http\Resources\PlantResource;
use App\Http\Resources\SizingOperationResource;
use App\Http\Resources\TaskResource;
use App\Models\Employee;
use App\Models\MachineNumber;
use App\Models\MachineType;
use App\Models\Plant;
use App\Models\SizingLog;
use App\Models\SizingOperation;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SizingOperationController extends Controller
{
    public function index()
    {

        $sizingoperations = SizingOperationResource::collection(
            SizingOperation::with(['plant', 'machineType', 'task', 'smallTask', 'employee', 'department', 'machineNumber', 'sizingLogs.employee'])
                ->where('department_id', 2) // Sizing Department ID
                ->where('status', 'running')
                ->orWhere('status', 'paused')
                ->orderBy('created_at', 'desc')
                ->get()
        );
        $employees = EmployeeResource::collection(Employee::where('department_id', 2)->get());
        $plants = PlantResource::collection(
            Plant::whereIn('id', [8, 9])->get()
        );
        $machinetypes = MachineTypeResource::collection(MachineType::where('department_id', 2)->get());

        $tasks = TaskResource::collection(Task::where('department_id', 2)->get());

        $machinenumbers = MachineNumberResource::collection(MachineNumber::all());

        return Inertia('SizingOperation/Index', [
            'employees' => $employees,
            'plants' => $plants,
            'machinetypes' => $machinetypes,
            'tasks' => $tasks,
            'machinenumbers' => $machinenumbers,
            'sizingoperations' => $sizingoperations,
        ]);
    }

    public function store(StoreSizingOperationRequest $request)
    {
        $sodata = $request->validated();

        $sodata['department_id'] = 2; // Sizing Department ID
        $sodata['status'] = 'running'; // Default status
        $sodata['start_time'] = now();
        $sodata['last_start_time'] = now();
        $sodata['worked_seconds'] = 0;
        $sodata['employee_id'] = $request->employee_id; // Temporary employee ID, to be replaced with auth user ID

        $operation = SizingOperation::create($sodata);

        // 2️⃣ Create first sizing log (employee start work)

        foreach ($request->team_ids as $employeeID) {
            SizingLog::create([
                'sizing_operation_id' => $operation->id,
                'employee_id' => $employeeID, // from form / auth
                'start_time' => now(),
                'last_start_time' => now(),
                'worked_seconds' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'Sizing operation started');
    }

    public function update(UpdateSizingOperationRequest $request, $id)
    {
        $operation = SizingOperation::with('sizingLogs')->findOrFail($id);

        $data = $request->validated();

        //  Update main operation
        $operation->update([
            'plant_id' => $data['plant_id'],
            'machine_type_id' => $data['machine_type_id'],
            'machine_number_id' => $data['machine_number_id'],
            'task_id' => $data['task_id'],
        ]);

        //  New team ids from form
        $newTeamIds = collect($request->team_ids ?? []);

        //  Existing logs employee ids
        $existingLogs = $operation->sizingLogs;
        $existingEmployeeIds = $existingLogs->pluck('employee_id');

        /**
         *  DELETE logs for removed employees
         */
        $logsToDelete = $existingLogs->whereNotIn(
            'employee_id',
            $newTeamIds
        );

        foreach ($logsToDelete as $log) {
            $log->delete(); // 🔥 completely remove
        }

        /**
         *  CREATE logs for newly added employees
         */
        $employeesToAdd = $newTeamIds->diff($existingEmployeeIds);

        foreach ($employeesToAdd as $employeeId) {
            SizingLog::create([
                'sizing_operation_id' => $operation->id,
                'employee_id' => $employeeId,
                'start_time' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Sizing operation updated');
    }

    public function complete($id)
    {
        $op = SizingOperation::findOrFail($id);
        $now = Carbon::now('Asia/Tokyo');

        //  already completed guard
        if ($op->status === 'completed') {
            return back();
        }

        $workedSeconds = $op->worked_seconds;

        //  if running → add last segment
        if ($op->status === 'running' && $op->last_start_time) {
            $start = Carbon::parse($op->last_start_time, 'Asia/Tokyo');

            if ($start->lte($now)) {
                $workedSeconds += $start->diffInSeconds($now);
            }
        }

        //  update operation
        $op->update([
            'status' => 'completed',
            'end_time' => $now,
            'worked_seconds' => $workedSeconds,
            'last_start_time' => null,
        ]);

        //  close all running logs
        $logs = SizingLog::where('sizing_operation_id', $op->id)
            ->whereNull('end_time')
            ->get();

        foreach ($logs as $log) {
            $worked = $log->worked_seconds;

            if ($log->last_start_time) {
                $start = Carbon::parse($log->last_start_time, 'Asia/Tokyo');

                if ($start->lte($now)) {
                    $worked += $start->diffInSeconds($now);
                }
            }

            $log->update([
                'worked_seconds' => $worked,
                'last_start_time' => null,
                'end_time' => $now,
            ]);
        }

        return back()->with('success', 'Sizing operation completed');
    }

    public function completelist()
    {

        $sizingoperations = SizingOperationResource::collection(
            SizingOperation::with(['plant', 'machineType', 'task', 'smallTask', 'employee', 'department', 'machineNumber', 'sizingLogs.employee'])
                ->where('department_id', 2) // Sizing Department ID
                ->where('status', 'completed') // Completed operations
                ->orderBy('created_at', 'desc')
                ->get()
        );

        return Inertia('Complete/SizingCompleteList', [
            'sizingoperations' => $sizingoperations,
        ]);
    }

    public function addEmployees(Request $request, $id)
    {
        $request->validate([
            'employee_ids' => ['required', 'array'],
            'employee_ids.*' => ['exists:employees,id'],
        ]);

        $operation = SizingOperation::findOrFail($id);
        $now = now();

        foreach ($request->employee_ids as $employeeID) {

            // already exists & not finished → skip
            $exists = SizingLog::where('sizing_operation_id', $operation->id)
                ->where('employee_id', $employeeID)
                ->whereNull('end_time')
                ->exists();

            if ($exists) {
                continue;
            }

            $data = [
                'sizing_operation_id' => $operation->id,
                'employee_id' => $employeeID,
                'worked_seconds' => 0,
                'paused_seconds' => 0,
            ];

            if ($operation->status === 'running') {
                $data['start_time'] = $now;
                $data['last_start_time'] = $now;
                $data['paused_at'] = null;
            } else { // paused
                $data['start_time'] = $now;
                $data['last_start_time'] = null;
                $data['paused_at'] = $now;
            }

            SizingLog::create($data);
        }

        return back()->with('success', 'Employees added');
    }

    public function stop($id)
    {
        $op = SizingOperation::findOrFail($id);

        // guard
        if ($op->status !== 'running' || empty($op->last_start_time)) {
            return back();
        }

        $now = now();
        $start = Carbon::parse($op->last_start_time);

        if ($start->greaterThan($now)) {
            return back()->with('error', '時間エラー');
        }

        // --- Operation ---
        $worked = $start->diffInSeconds($now);

        $op->update([
            'worked_seconds' => $op->worked_seconds + $worked,
            'last_start_time' => null,
            'status' => 'paused',
            'paused_time' => $now,

        ]);

        // --- Logs (running ones only) ---
        $logs = SizingLog::where('sizing_operation_id', $op->id)
            ->whereNotNull('last_start_time')
            ->get();

        foreach ($logs as $log) {
            $logStart = Carbon::parse($log->last_start_time);

            if ($logStart->greaterThan($now)) {
                continue; // safety
            }

            $logWorked = $logStart->diffInSeconds($now);

            $log->update([
                'worked_seconds' => $log->worked_seconds + $logWorked,
                'last_start_time' => null,
                'paused_time' => $now,

            ]);
        }

        return back()->with('success', '作業を停止しました');
    }

    public function resume($id)
    {
        $op = SizingOperation::findOrFail($id);

        if ($op->status !== 'paused' || empty($op->paused_time)) {
            return back();
        }

        $now = now();

        // --- Operation paused time ---
        $pausedSeconds = Carbon::parse($op->paused_time)->diffInSeconds($now);

        $op->update([
            'paused_seconds' => $op->paused_seconds + $pausedSeconds,
            'paused_time' => null,
            'last_start_time' => $now,
            'status' => 'running',
        ]);

        // --- Logs resume ---
        $logs = $op->sizingLogs()
            ->whereNull('end_time')
            ->get();

        foreach ($logs as $log) {

            if ($log->paused_time) {
                $logPaused = Carbon::parse($log->paused_time)->diffInSeconds($now);

                $log->update([
                    'paused_seconds' => $log->paused_seconds + $logPaused,
                    'paused_time' => null,
                    'last_start_time' => $now,
                ]);
            } else {
                // safety fallback
                $log->update([
                    'last_start_time' => $now,
                ]);
            }
        }

        return back()->with('success', '作業を再開しました');
    }
}
