<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\SizingOperationsExport;
use App\Http\Requests\ExportSizingOperationRequest;
use App\Http\Requests\StoreSizingOperationRequest;
use App\Http\Requests\UpdateSizingOperationRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\MachineNumberResource;
use App\Http\Resources\MachineTypeResource;
use App\Http\Resources\PlantResource;
use App\Http\Resources\SizingOperationResource;
use App\Http\Resources\SmallTaskResource;
use App\Http\Resources\TaskResource;
use App\Models\Employee;
use App\Models\MachineNumber;
use App\Models\MachineType;
use App\Models\Plant;
use App\Models\SizingLog;
use App\Models\SizingOperation;
use App\Models\Task;
use App\Models\SmallTask;

use Carbon\Carbon;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;


class NenshiOperationController extends Controller
{
    public function index()
    {

        $sizingoperations = SizingOperationResource::collection(
            SizingOperation::with(['plant', 'machineType', 'task', 'smallTask', 'employee', 'department', 'machineNumber', 'sizingLogs.employee'])
                ->where('department_id', 1) // Nenshi Department ID
                ->whereIn('status', ['running', 'paused'])
                ->orderBy('created_at', 'desc')
                ->get()
        );
        $employees = EmployeeResource::collection(Employee::where('department_id', 1)->get());
        $plants = PlantResource::collection(
            Plant::whereIn('id', [1,2,3,4,5,6,7])->get()
        );
        $machinetypes = MachineTypeResource::collection(MachineType::where('department_id', 1)->get());

        $tasks = TaskResource::collection(Task::where('department_id', 1)->get());

        $machinenumbers = MachineNumberResource::collection(MachineNumber::all());

         $smalltasks = SmallTaskResource::collection(SmallTask::all());

        return Inertia('NenshiOperation/Index', [
            'employees' => $employees,
            'plants' => $plants,
            'machinetypes' => $machinetypes,
            'tasks' => $tasks,
            'machinenumbers' => $machinenumbers,
            'sizingoperations' => $sizingoperations,
            'smalltasks' => $smalltasks,
        ]);
    }


    public function store(StoreSizingOperationRequest $request)
    {
        $sodata = $request->validated();

        $sodata['department_id'] = 1; // Nenshi Department ID
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
            ->where('department_id', 1)
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

        return Inertia::render('Complete/NenshiCompleteList', [
            'sizingoperations' => $sizingoperations,
            'machinetypes' => MachineTypeResource::collection(
                MachineType::where('department_id', 1)->get()
            ),
            'tasks' => TaskResource::collection(
                Task::where('department_id', 1)
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
            ->where('department_id', 1)
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

        return Inertia::render('Complete/NenshiAdminCompleteList', [
            'sizingoperations' => $sizingoperations,
            'machinetypes' => MachineTypeResource::collection(
                MachineType::where('department_id', 1)->get()
            ),
            'tasks' => TaskResource::collection(
                Task::where('department_id', 1)
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


}
