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

    public function destroy(Task $task)

    {
        $task->delete();


        return redirect ()->route('tasks.index');
    }



}
