<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\MachineTypeResource;
use App\Http\Resources\TaskResource;
use App\Models\Department;
use App\Models\MachineType;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index( Request $request)
    {

        $query = Task::with('machineType', 'department')
            ->join('machine_types', 'tasks.machine_type_id', '=', 'machine_types.id')
            ->select('tasks.*')
            ->orderBy('machine_types.name') 
            ->orderBy('tasks.name');        


        if ($request->filled('machine_type_id')) {
            $query->where('tasks.machine_type_id', $request->machine_type_id);
        }

        $tasks = $query
            ->orderBy('machine_types.name')
            ->orderBy('tasks.name')
            ->get();


        
        
        $machinetypes = MachineTypeResource::collection(MachineType::all());

        return inertia('Task/Index', [
            'tasks' => TaskResource::collection($tasks),
            'machinetypes' => $machinetypes,
            'filters' => $request->only(['machine_type_id']),
        ]);
    }

    public function create()
    {
        $departments = DepartmentResource::collection(Department::all());
        $machineTypes = MachineTypeResource::collection(MachineType::all());

        return inertia('Task/Create', [
            'machineTypes' => $machineTypes,
            'departments' => $departments,
        ]);
    }

    public function store(StoreTaskRequest $request)
    {
        Task::create($request->validated());

        return redirect()->route('tasks.index');
    }

    public function edit(Task $task)
    {

        $machineTypes = MachineTypeResource::collection(MachineType::all());
        $departments = DepartmentResource::collection(Department::all());

        return inertia('Task/Edit', [
            'task' => new TaskResource($task->load('machineType', 'department')),
            'machineTypes' => $machineTypes,
            'departments' => $departments,
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {

        $task->update($request->validated());

        return redirect()->route('tasks.index');

    }

    public function getTasksByMachineType(Request $request)
    {
        $typeId = $request->machine_type_id;

        \Log::info('Request machine_type_id: '.$typeId);

        if (! $typeId) {
            return response()->json([]);
        }

        try {
            $tasks = Task::where('machine_type_id', $typeId)->get();
            \Log::info('Tasks fetched: '.$tasks->count());

            return response()->json($tasks);
        } catch (\Exception $e) {
            \Log::error('Task fetch error: '.$e->getMessage());

            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function destroy(Task $task)
    {
        // if ($task->is_drive_task) {
        //     return back()->withErrors('運転作業は削除できません');
        // }
        $task->delete();

        return redirect()->route('tasks.index');
    }
}
