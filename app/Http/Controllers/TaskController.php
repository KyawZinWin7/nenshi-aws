<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Task;
use App\Http\Resources\TaskResource;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
      public function index()
    {

        $tasks = TaskResource::collection(Task::all());
        return inertia('Task/Index',[
            'tasks'=> $tasks,
        ]);
    }

     public function create()

    {
        return inertia('Task/Create');
    }


    public function store(StoreTaskRequest $request)
    
    {
        Task::create($request->validated());


        return redirect()->route('tasks.index');
    }


    public function edit(Task $task)
    {
        return inertia('Task/Edit',[
            'task'=> TaskResource::make($task)
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
