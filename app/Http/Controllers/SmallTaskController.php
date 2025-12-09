<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSmallTaskRequest;
use App\Http\Requests\UpdateSmallTaskRequest;
use App\Http\Resources\MachineTypeResource;
use App\Http\Resources\SmallTaskResource;
use App\Models\MachineType;
use App\Models\SmallTask;
use Illuminate\Http\Request;

class SmallTaskController extends Controller
{
    public function index()
    {
        $smalltasks = SmallTask::with('machineType')->get();

        return inertia('SmallTask/Index', [
            'smalltasks' => SmallTaskResource::collection($smalltasks),
        ]);
    }

    public function create()
    {
        $machinetypes = MachineTypeResource::collection(MachineType::all());

        return inertia('SmallTask/Create', [
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

        return inertia('SmallTask/Edit', [
            'smalltask' => new SmallTaskResource($smalltask->load('machineType')),
            'machinetypes' => $machinetypes,
        ]);
    }

    public function update(UpdateSmallTaskRequest $request, SmallTask $smalltask)
    {
        $smalltask->update($request->validated());

        return redirect()->route('smalltasks.index');
    }

    public function getSmallTasksByMachineType(Request $request)
    {
        $typeId = $request->machine_type_id;

        \Log::info('Request machine_type_id: '.$typeId);

        if (! $typeId) {
            return response()->json([]);
        }

        try {
            $smalltasks = SmallTask::where('machine_type_id', $typeId)->get();
            \Log::info('SmallTasks fetched: '.$smalltasks->count());

            return response()->json($smalltasks);
        } catch (\Exception $e) {
            \Log::error('SmallTask fetch error: '.$e->getMessage());

            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function destroy(SmallTask $smalltask)
    {
        $smalltask->delete();

        return redirect()->route('smalltasks.index');
    }
}
