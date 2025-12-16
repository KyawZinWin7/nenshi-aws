<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Http\Resources\PlantResource;
use App\Http\Resources\MachineTypeResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\MachineNumberResource;
use App\Models\Employee;
use App\Models\Plant;
use App\Models\MachineType;
use App\Models\MachineNumber;
use App\Models\Task;

class SizingOperationController extends Controller
{
    public function index()
    {

        $employees = EmployeeResource::collection(Employee::where('department_id', 2)->get());
        $plants = PlantResource::collection(
            Plant::whereIn('id', [8, 9])->get()
        );
        $machinetypes = MachineTypeResource::collection(MachineType::where('department_id',2)->get());

        $tasks = TaskResource::collection(Task::where('department_id',2)->get());

        $machinenumbers = MachineNumberResource::collection(MachineNumber::all());

        return Inertia('SizingOperation/Index', [
            'employees' => $employees,
            'plants' => $plants,
            'machinetypes'=> $machinetypes,
            'tasks' => $tasks,
            'machinenumbers' => $machinenumbers,
        ]);
    }
}
