<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Http\Resources\MachineNumberResource;
use App\Http\Resources\MachineTypeResource;
use App\Http\Resources\PlantResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\SizingOperationResource;
use App\Http\Requests\StoreSizingOperationRequest;

use App\Models\Employee;
use App\Models\MachineNumber;
use App\Models\MachineType;
use App\Models\Plant;
use App\Models\Task;
use App\Models\SizingOperation;
use App\Models\SizingLog;
use Inertia\Inertia;

class SizingOperationController extends Controller
{
    public function index()
    {
        
        $sizingoperations = SizingOperationResource::collection(
            SizingOperation::with(['plant', 'machineType', 'task', 'smallTask', 'employee', 'department', 'machineNumber', 'sizingLogs.employee'])
                ->where('department_id', 2) // Sizing Department ID
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

        foreach($request->team_ids as $employeeID){
            SizingLog::create([
            'sizing_operation_id' => $operation->id,
            'employee_id' => $employeeID, // from form / auth
            'start_time' => now(),
        ]);
        }
        

        return redirect()->back()->with('success', 'Sizing operation started');
    }
}
