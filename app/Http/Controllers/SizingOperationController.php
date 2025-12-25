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
use Inertia\Inertia;

class SizingOperationController extends Controller
{
    public function index()
    {

        $sizingoperations = SizingOperationResource::collection(
            SizingOperation::with(['plant', 'machineType', 'task', 'smallTask', 'employee', 'department', 'machineNumber', 'sizingLogs.employee'])
                ->where('department_id', 2) // Sizing Department ID
                ->where('status', 0) // Ongoing operations
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
        $sodata['status'] = 0; // Default status
        $sodata['start_time'] = now();
        $sodata['employee_id'] = $request->employee_id; // Temporary employee ID, to be replaced with auth user ID

        $operation = SizingOperation::create($sodata);

        // 2️⃣ Create first sizing log (employee start work)

        foreach ($request->team_ids as $employeeID) {
            SizingLog::create([
                'sizing_operation_id' => $operation->id,
                'employee_id' => $employeeID, // from form / auth
                'start_time' => now(),
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
        $sizingOperation = SizingOperation::findOrFail($id);
        $endTime = Carbon::now('Asia/Tokyo');

        // --- Operation time ---
        $opStart = Carbon::parse($sizingOperation->start_time, 'Asia/Tokyo');

        if ($endTime->lt($opStart)) {
            return back()->with('error', '終了時間は開始時間より後でなければなりません。');
        }

        $opSeconds = $opStart->diffInSeconds($endTime);

        $sizingOperation->update([
            'status' => 1,
            'end_time' => $endTime,
            'total_time' => $opSeconds, // seconds
        ]);

        // --- Logs (same sizing_operation_id, all users) ---
        $logs = SizingLog::where('sizing_operation_id', $sizingOperation->id)
            ->whereNull('end_time')
            ->get();

        foreach ($logs as $log) {
            if (! $log->start_time) {
                continue; // safety
            }

            $logStart = Carbon::parse($log->start_time, 'Asia/Tokyo');
            $logSeconds = $logStart->diffInSeconds($endTime);

            $log->update([
                'end_time' => $endTime,
                'duration' => $logSeconds, // seconds
            ]);
        }

        return back()->with('success', 'Sizing operation completed');
    }

    public function completelist()
    {

        $sizingoperations = SizingOperationResource::collection(
            SizingOperation::with(['plant', 'machineType', 'task', 'smallTask', 'employee', 'department', 'machineNumber', 'sizingLogs.employee'])
                ->where('department_id', 2) // Sizing Department ID
                ->where('status', 1) // Completed operations
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

        foreach ($request->employee_ids as $employeeID) {

            // already exists & not finished → skip
            $exists = SizingLog::where('sizing_operation_id', $operation->id)
                ->where('employee_id', $employeeID)
                ->whereNull('end_time')
                ->exists();

            if ($exists) {
                continue;
            }

            // create new log
            SizingLog::create([
                'sizing_operation_id' => $operation->id,
                'employee_id' => $employeeID,
                'start_time' => now(),
            ]);
        }

        return back()->with('success', 'Employees added');
    }
}
