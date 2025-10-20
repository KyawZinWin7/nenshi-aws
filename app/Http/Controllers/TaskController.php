<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Task;
use App\Models\MachineType;
use App\Http\Resources\TaskResource;
use App\Http\Resources\MachineTypeResource;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
      public function index()
    {
        
        $tasks = TaskResource::collection(Task::with('machineType')->get());
        return inertia('Task/Index',[
            'tasks'=> $tasks,
        ]);
    }

     public function create()

    {
        $machineTypes = MachineTypeResource::collection(MachineType::all());
        return inertia('Task/Create',[
            'machineTypes' => $machineTypes
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

        return inertia('Task/Edit',[
            'task'=> new TaskResource($task->load('machineType')),
            'machineTypes' => $machineTypes
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

    \Log::info('Request machine_type_id: ' . $typeId);

    if (!$typeId) {
        return response()->json([]);
    }

    try {
        $tasks = Task::where('machine_type_id', $typeId)->get();
        \Log::info('Tasks fetched: ' . $tasks->count());
        return response()->json($tasks);
    } catch (\Exception $e) {
        \Log::error("Task fetch error: " . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
}



    public function destroy(Task $task)

    {
        $task->delete();


        return redirect ()->route('tasks.index');
    }



}
