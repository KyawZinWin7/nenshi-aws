<?php

namespace App\Http\Controllers;

use App\Exports\SizingOperationsExport;
use App\Http\Requests\ExportSizingOperationRequest;
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
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;


class SizingOperationController extends Controller
{
    public function index()
    {

        $sizingoperations = SizingOperationResource::collection(
            SizingOperation::with(['plant', 'machineType', 'task', 'smallTask', 'employee', 'department', 'machineNumber', 'sizingLogs.employee'])
                ->where('department_id', 2) // Sizing Department ID
                ->whereIn('status', ['running', 'paused'])
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
            // 'last_start_time' => now(),
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
                'worked_seconds' => 0,
                'last_start_time' => now(),
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

        $endTime = $op->status === 'paused'
            ? $op->paused_time
            : $now;
        $op->update([
            'status' => 'completed',
            'end_time' => $endTime,
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
                'end_time' => $endTime,
            ]);
        }

        return back()->with('success', 'Sizing operation completed');
    }

    public function uncomplete($id)
    {
        $op = SizingOperation::findOrFail($id);
        $now = Carbon::now('Asia/Tokyo');

        // not completed guard
        if ($op->status !== 'completed') {
            return back();
        }

        // save old end time BEFORE update
        $oldEndTime = $op->end_time;

        // reopen operation as running
        $op->update([
            'status' => 'running',
            'end_time' => null,
            'last_start_time' => $now,
        ]);

        // reopen logs that were closed at completion time
        $logs = SizingLog::where('sizing_operation_id', $op->id)
            ->where('end_time', $oldEndTime)
            ->get();

        foreach ($logs as $log) {
            $log->update([
                'end_time' => null,
                'last_start_time' => $now,
            ]);
        }

        return back()->with('success', 'Sizing operation uncompleted and running');
    }

    public function completelist(Request $request)
    {
        $query = SizingOperation::with([
            'plant',
            'machineType',
            'task',
            'smallTask',
            'employee',
            'department',
            'machineNumber',
            'sizingLogs.employee',
        ])
            ->where('department_id', 2)
            ->where('status', 'completed');

        //  Search (工場・日付・担当者)
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('plant', fn ($q) => $q->where('name', 'like', "%{$search}%")
                )
                    ->orWhereDate('created_at', $search)
                    ->orWhereHas('employee', fn ($q) => $q->where('name', 'like', "%{$search}%")
                    );
            });
        }

        //  Machine Type filter
        if ($request->filled('machine_type_id')) {
            $query->where('machine_type_id', $request->machine_type_id);
        }

        //  Task multi filter
        if ($request->filled('tasks')) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->whereIn('name', $request->tasks);
            });
        }

        $sizingoperations = $query
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString()
            ->through(fn ($op) => new SizingOperationResource($op));

        return Inertia::render('Complete/SizingCompleteList', [
            'sizingoperations' => $sizingoperations,
            'machinetypes' => MachineTypeResource::collection(
                MachineType::where('department_id', 2)->get()
            ),
            'tasks' => TaskResource::collection(
                Task::where('department_id', 2)
                    ->select('name')
                    ->distinct()
                    ->orderBy('name')
                    ->get()
            ),
            'filters' => $request->only([
                'search',
                'machine_type_id',
                'tasks',
            ]),
        ]);
    }

    public function admincompletelist(Request $request)
    {
        $query = SizingOperation::with([
            'plant',
            'machineType',
            'task',
            'smallTask',
            'employee',
            'department',
            'machineNumber',
            'sizingLogs.employee',
        ])
            ->where('department_id', 2)
            ->where('status', 'completed');

        //  Search (工場・日付・担当者)
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('plant', fn ($q) => $q->where('name', 'like', "%{$search}%")
                )
                    ->orWhereDate('created_at', $search)
                    ->orWhereHas('employee', fn ($q) => $q->where('name', 'like', "%{$search}%")
                    );
            });
        }

        //  Machine Type filter
        if ($request->filled('machine_type_id')) {
            $query->where('machine_type_id', $request->machine_type_id);
        }

        //  Task multi filter
        if ($request->filled('tasks')) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->whereIn('name', $request->tasks);
            });
        }

        $sizingoperations = $query
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString()
            ->through(fn ($op) => new SizingOperationResource($op));

        return Inertia::render('Complete/SizingAdminCompleteList', [
            'sizingoperations' => $sizingoperations,
            'machinetypes' => MachineTypeResource::collection(
                MachineType::where('department_id', 2)->get()
            ),
            'tasks' => TaskResource::collection(
                Task::where('department_id', 2)
                    ->select('name')
                    ->distinct()
                    ->orderBy('name')
                    ->get()
            ),
            'filters' => $request->only([
                'search',
                'machine_type_id',
                'tasks',
            ]),
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
                // 'end_time' => $now,

            ]);
        }

        return back()->with('success', '作業を停止しました');
    }

    // public function resume(Request $request, $id)
    // {

    //     $op = SizingOperation::with('sizinglogs')->findOrFail($id);

    //     Log::info('RESUME HIT', [
    //         'op_id' => $op->id,
    //         'status' => $op->status,
    //         'department' => $op->department_id,
    //         'logs' => $op->sizinglogs->map(fn ($l) => [
    //             'id' => $l->id,
    //             'paused' => $l->paused_time,
    //             'end' => $l->end_time,
    //         ]),
    //     ]);

    //     $now = Carbon::now('Asia/Tokyo');

    //     // validation
    //     $request->validate([
    //         'team_ids' => 'nullable|array',
    //         'team_ids.*' => 'exists:employees,id',
    //     ]);

    //     // ===== resume operation =====
    //     $pausedSeconds = 0;
    //     if ($op->paused_time) {
    //         $pausedSeconds = Carbon::parse($op->paused_time)->diffInSeconds($now);
    //     }

    //     $op->update([
    //         'paused_seconds' => $op->paused_seconds + $pausedSeconds,
    //         'paused_time' => null,
    //         'last_start_time' => $now,
    //         'status' => 'running',
    //     ]);

    //     // -------------------------------------------------
    //     // 2. GET ACTIVE LOGS (not completed)
    //     // -------------------------------------------------
    //     $activeLogs = $op->sizingLogs()
    //         ->whereNull('end_time')
    //         ->get();

    //     // -------------------------------------------------
    //     // 3. HANDLE GUEST LOG (employee_id = NULL)
    //     // -------------------------------------------------
    //     foreach ($activeLogs as $log) {

    //         if (! is_null($log->employee_id)) {
    //             continue;
    //         }

    //         if ($log->paused_time) {
    //             $paused = Carbon::parse($log->paused_time, 'Asia/Tokyo')
    //                 ->diffInSeconds($now);

    //             $log->update([
    //                 'paused_seconds' => $log->paused_seconds + $paused,
    //                 'paused_time' => null,
    //                 'last_start_time' => $now,
    //             ]);
    //         }

    //         // Guest has no team logic → skip rest
    //         continue;
    //     }

    //     // -------------------------------------------------
    //     // 4. TEAM-BASED LOGIC (LOGIN USERS)
    //     // -------------------------------------------------
    //     $teamIds = $request->input('team_ids', []);

    //     // existing employee logs (exclude guest)
    //     $existingLogs = $activeLogs
    //         ->whereNotNull('employee_id');

    //     $existingEmployeeIds = $existingLogs
    //         ->pluck('employee_id')
    //         ->unique()
    //         ->toArray();

    //     // -------------------------------------------------
    //     // 4-1. COMPLETE REMOVED EMPLOYEES
    //     // -------------------------------------------------
    //     $removedEmployeeIds = array_diff($existingEmployeeIds, $teamIds);

    //     foreach ($existingLogs as $log) {

    //         if (! in_array($log->employee_id, $removedEmployeeIds)) {
    //             continue;
    //         }

    //         $workedSeconds = $log->worked_seconds;

    //         if ($log->last_start_time) {
    //             $start = Carbon::parse($log->last_start_time, 'Asia/Tokyo');
    //             $workedSeconds += $start->diffInSeconds($now);
    //         }

    //         $log->update([
    //             'end_time' => $now,
    //             'worked_seconds' => $workedSeconds,
    //             'last_start_time' => null,
    //             'paused_time' => null,
    //         ]);
    //     }

    //     // -------------------------------------------------
    //     // 4-2. RESUME REMAINING EMPLOYEES
    //     // -------------------------------------------------
    //     foreach ($existingLogs as $log) {

    //         if (in_array($log->employee_id, $removedEmployeeIds)) {
    //             continue;
    //         }

    //         if ($log->paused_time) {
    //             $paused = Carbon::parse($log->paused_time, 'Asia/Tokyo')
    //                 ->diffInSeconds($now);

    //             $log->update([
    //                 'paused_seconds' => $log->paused_seconds + $paused,
    //                 'paused_time' => null,
    //                 'last_start_time' => $now,
    //             ]);
    //         } else {
    //             // safety: ensure running logs have start time
    //             $log->update([
    //                 'last_start_time' => $now,
    //             ]);
    //         }
    //     }

    //     // -------------------------------------------------
    //     // 4-3. ADD NEW EMPLOYEES
    //     // -------------------------------------------------
    //     $newEmployeeIds = array_diff($teamIds, $existingEmployeeIds);

    //     foreach ($newEmployeeIds as $employeeId) {
    //         $op->sizingLogs()->create([
    //             'employee_id' => $employeeId,
    //             'start_time' => $now,
    //             'last_start_time' => $now,
    //             'worked_seconds' => 0,
    //             'paused_seconds' => 0,
    //         ]);
    //     }

    //     return back()->with('success', '作業を再開しました');
    // }

    public function resume(Request $request, $operation)
    {
        return DB::transaction(function () use ($request, $operation) {

            $op = SizingOperation::with('sizingLogs')->findOrFail($operation);
            $now = now();

            // -------------------------------------------------
            // 0. must be paused
            // -------------------------------------------------
            if (! ($op->paused_time instanceof \Carbon\Carbon)) {
                Log::warning('RESUME SKIPPED - not paused', [
                    'op_id' => $op->id,
                    'status' => $op->status,
                    'paused_time' => $op->paused_time,
                ]);

                return response()->json([
                    'message' => 'Operation is not paused',
                ], 200);
            }

            // -------------------------------------------------
            // validation
            // -------------------------------------------------
            $request->validate([
                'team_ids' => 'required|array',
                'team_ids.*' => 'exists:employees,id',
            ]);

            // -------------------------------------------------
            // 1. resume operation (NULL-SAFE)
            // -------------------------------------------------
            $pausedSeconds = 0;
            if ($op->paused_time instanceof \Carbon\Carbon) {
                $pausedSeconds = $op->paused_time->diffInSeconds($now);
            }

            $op->update([
                'paused_seconds' => ($op->paused_seconds ?? 0) + $pausedSeconds,
                'paused_time' => null,
                'last_start_time' => $now,
                'status' => 'running',
            ]);

            // -------------------------------------------------
            // 2. active logs
            // -------------------------------------------------
            $activeLogs = $op->sizingLogs()
                ->whereNull('end_time')
                ->get();

            // -------------------------------------------------
            // 3. guest logs (employee_id = NULL)
            // -------------------------------------------------
            foreach ($activeLogs as $log) {

                if ($log->employee_id !== null) {
                    continue;
                }

                if ($log->paused_time instanceof \Carbon\Carbon) {
                    $paused = $log->paused_time->diffInSeconds($now);

                    $log->update([
                        'paused_seconds' => ($log->paused_seconds ?? 0) + $paused,
                        'paused_time' => null,
                        'last_start_time' => $now,
                    ]);
                }
            }

            // -------------------------------------------------
            // 4. team logic
            // -------------------------------------------------
            $teamIds = $request->input('team_ids', []);
            if (! is_array($teamIds)) {
                $teamIds = [];
            }

            $existingLogs = $activeLogs->whereNotNull('employee_id');
            $existingEmployeeIds = $existingLogs
                ->pluck('employee_id')
                ->unique()
                ->toArray();

            // -------------------------------------------------
            // 4-1. removed employees
            // -------------------------------------------------
            $removedEmployeeIds = array_diff($existingEmployeeIds, $teamIds);

            foreach ($existingLogs as $log) {

                if (! in_array($log->employee_id, $removedEmployeeIds)) {
                    continue;
                }

                $workedSeconds = $log->worked_seconds ?? 0;

                if ($log->last_start_time instanceof \Carbon\Carbon) {
                    $workedSeconds += $log->last_start_time->diffInSeconds($now);
                }

                $log->update([
                    'end_time' => $now,
                    'worked_seconds' => $workedSeconds,
                    'last_start_time' => null,
                    'paused_time' => null,
                ]);
            }

            // -------------------------------------------------
            // 4-2. resume remaining employees
            // -------------------------------------------------
            foreach ($existingLogs as $log) {

                if (in_array($log->employee_id, $removedEmployeeIds)) {
                    continue;
                }

                if ($log->paused_time instanceof \Carbon\Carbon) {
                    $paused = $log->paused_time->diffInSeconds($now);

                    $log->update([
                        'paused_seconds' => ($log->paused_seconds ?? 0) + $paused,
                        'paused_time' => null,
                        'last_start_time' => $now,
                    ]);
                } else {
                    $log->update([
                        'last_start_time' => $now,
                    ]);
                }
            }

            // -------------------------------------------------
            // 4-3. add new employees
            // -------------------------------------------------
            $newEmployeeIds = array_diff($teamIds, $existingEmployeeIds);

            foreach ($newEmployeeIds as $employeeId) {
                $op->sizingLogs()->create([
                    'employee_id' => $employeeId,
                    'start_time' => $now,
                    'last_start_time' => $now,
                    'worked_seconds' => 0,
                    'paused_seconds' => 0,
                ]);
            }

            return response()->json([
                'message' => '作業を再開しました',
            ], 200);
        });
    }

    public function export()
    {
        $sizingoperations = SizingOperationResource::collection(
            SizingOperation::with(['plant', 'machineType', 'task', 'smallTask', 'employee', 'department', 'machineNumber', 'sizingLogs.employee'])
                ->where('department_id', 2) // Sizing Department ID
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->get()
        );

        $machinetypes = MachineTypeResource::collection(MachineType::where('department_id', 2)->get());
        $tasks = TaskResource::collection(Task::where('department_id', 2)->get());
        $plants = PlantResource::collection(
            Plant::whereIn('id', [8, 9])->get()
        );
        $employees = EmployeeResource::collection(Employee::where('department_id', 2)->get());

        return Inertia('Report/SizingExport', [
            'sizingoperations' => $sizingoperations,
            'machinetypes' => $machinetypes,
            'tasks' => $tasks,
            'plants' => $plants,
            'employees' => $employees,
        ]);
    }

    public function exportStore(ExportSizingOperationRequest $request)
    {
        try {
            $filters = $request->validated();

            $exists = SizingOperation::query()
                ->when(! empty($filters['date_from']),
                    fn ($q) => $q->whereDate('start_time', '>=', $filters['date_from'])
                )
                ->when(! empty($filters['date_to']),
                    fn ($q) => $q->whereDate('start_time', '<=', $filters['date_to'])
                )
                ->when(! empty($filters['employee_id']), function ($q) use ($filters) {
                    $q->whereHas('sizingLogs', function ($q) use ($filters) {
                        $q->where('employee_id', $filters['employee_id']);
                    });
                })

                ->when(! empty($filters['machine_type_id']),
                    fn ($q) => $q->where('machine_type_id', $filters['machine_type_id'])
                )
                ->when(! empty($filters['task_id']),
                    fn ($q) => $q->where('task_id', $filters['task_id'])
                )
                ->when(! empty($filters['plant_id']),
                    fn ($q) => $q->where('plant_id', $filters['plant_id'])
                )
                ->when(! empty($filters['machine_number']), function ($q) use ($filters) {
                    $q->whereHas('machineNumber', function ($q) use ($filters) {
                        $q->where('number', $filters['machine_number']);
                    });
                })
                ->exists();

            if (! $exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'エクスポートできるデータがありません。',
                ], 404);
            }

            return Excel::download(
                new SizingOperationsExport($filters),
                'sizing_operations.xlsx'
            );

        } catch (\Exception $e) {
            \Log::error('Sizing Export Error: '.$e->getMessage());

            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $operation = SizingOperation::findOrFail($id);

        // only allow deletion if not completed
        if ($operation->status === 'completed') {
            return back()->with('error', 'Completed operations cannot be deleted.');
        }

        // delete related sizing logs
        SizingLog::where('sizing_operation_id', $operation->id)->delete();

        // delete the operation
        $operation->delete();

        return back()->with('success', 'Sizing operation deleted successfully.');
    }
}
