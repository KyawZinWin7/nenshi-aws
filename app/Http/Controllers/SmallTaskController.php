<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\MachineTypeResource;
use App\Models\MachineType;
use App\Http\Requests\StoreSmallTaskRequest;
use App\Http\Requests\UpdateSmallTaskRequest;
use App\Models\SmallTask;
use App\Http\Resources\SmallTaskResource;


class SmallTaskController extends Controller
{
    public function index ()
    {
        $smalltasks = SmallTask::with('machineType')->get();
        
        return inertia('SmallTask/Index', [
            'smalltasks' => SmallTaskResource::collection($smalltasks),
        ]);
    }


    public function create()
    {
        $machinetypes = MachineTypeResource::collection(MachineType::all());
        return inertia('SmallTask/Create',[
            'machinetypes' => $machinetypes,
        ]);
    }

    public function store(StoreSmallTaskRequest $request)
    {
        $smalltask = SmallTask::create($request->validated());

        return redirect()->route('smalltasks.index');
    }

    public function edit(SmallTask $smalltask)
    {
        $machinetypes = MachineTypeResource::collection(MachineType::all());
        return inertia('SmallTask/Edit',[
            'smalltask' => new SmallTaskResource($smalltask->load('machineType')),
            'machinetypes' => $machinetypes,
        ]);
    }


    public function update(UpdateSmallTaskRequest $request, SmallTask $smalltask)
    {
        $smalltask->update($request->validated());

        return redirect()->route('smalltasks.index');
    }




    public function destroy(SmallTask $smalltask)
    {
        $smalltask->delete();

        return redirect()->route('smalltasks.index');
    }
}