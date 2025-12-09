<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMachineNumberRequest;
use App\Http\Requests\UpdateMachineNumberRequest;
use App\Http\Resources\MachineNumberResource;
use App\Http\Resources\MachineTypeResource;
use App\Http\Resources\PlantResource;
use App\Models\MachineNumber;
use App\Models\MachineType;
use App\Models\MachineTypePlant;
use App\Models\Plant;

class MachineNumberController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // SUPERADMIN → all data
        if ($user->role === 'superadmin') {
            $machineNumbers = MachineNumber::with([
                'machineTypePlant.machineType',
                'machineTypePlant.plant',
            ])->get();
        }
        // NOT SUPERADMIN → filter by department
        else {

            $machineNumbers = MachineNumber::whereHas('machineTypePlant.machineType', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })
                ->with([
                    'machineTypePlant.machineType',
                    'machineTypePlant.plant',
                ])
                ->get();
        }

        return inertia('MachineNumbers/Index', [
            'machineNumbers' => MachineNumberResource::collection($machineNumbers),
        ]);
    }

    public function create()
    {
        $machineTypes = MachineTypeResource::collection(MachineType::all());
        $plants = PlantResource::collection(Plant::all());

        return inertia('MachineNumbers/Create', [
            'machineTypes' => $machineTypes,
            'plants' => $plants,
        ]);
    }

    public function store(StoreMachineNumberRequest $request)
    {
        $validated = $request->validated();

        // 1️ machine_type_plant
        $relation = MachineTypePlant::firstOrCreate([
            'plant_id' => $validated['plant_id'],
            'machine_type_id' => $validated['machine_type_id'],
        ]);

        // 2️ number range  generate
        for ($i = $validated['start_number']; $i <= $validated['end_number']; $i++) {
            MachineNumber::firstOrCreate([
                'machine_type_plant_id' => $relation->id,
                'number' => $i,
            ]);
        }

        return redirect()->route('machinenumbers.index')->with('success', '登録しました！');
    }

    public function edit(MachineNumber $machinenumber)
    {
        $machineTypes = MachineTypeResource::collection(MachineType::all());
        $plants = PlantResource::collection(Plant::all());

        //machine_type_plant_id အတွက် number range
        $allNumbers = MachineNumber::where('machine_type_plant_id', $machinenumber->machine_type_plant_id)
            ->pluck('number');

        $startNumber = $allNumbers->min();
        $endNumber = $allNumbers->max();

        return inertia('MachineNumbers/Edit', [
            'machinenumber' => new MachineNumberResource(
                $machinenumber->load('machineTypePlant.machineType', 'machineTypePlant.plant')
            ),
            'machineTypes' => $machineTypes,
            'plants' => $plants,
            'startNumber' => $startNumber,
            'endNumber' => $endNumber,
        ]);
    }

    public function update(UpdateMachineNumberRequest $request, MachineNumber $machinenumber)
    {
        $validated = $request->validated();

        // machine_type_plant  update (なければ作成)
        $relation = MachineTypePlant::firstOrCreate([
            'plant_id' => $validated['plant_id'],
            'machine_type_id' => $validated['machine_type_id'],
        ]);

        // number range 全削除 (その type_plant_id に紐づく)
        MachineNumber::where('machine_type_plant_id', $machinenumber->machine_type_plant_id)->delete();

        // 新しい番号範囲を再生成
        for ($i = $validated['start_number']; $i <= $validated['end_number']; $i++) {
            MachineNumber::firstOrCreate([
                'machine_type_plant_id' => $relation->id,
                'number' => $i,
            ]);
        }

        return redirect()->route('machinenumbers.index')->with('success', '更新しました！');
    }

    public function destroy(MachineNumber $machinenumber)
    {
        $machinenumber->delete();

        return redirect()->route('machinenumbers.index');
    }
}
