<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MachineType;
use App\Models\MainOperation;
use App\Models\Plant;
use App\Http\Resources\MachineTypeResource;
use App\Http\Requests\StoreMachineTypeRequest;
use App\Http\Requests\UpdateMachineTypeRequest;
use App\Http\Resources\PlantResource;

class MachineTypeController extends Controller
{
    


    public function index()
    {

        $machinetypes = MachineTypeResource::collection(MachineType::with('plant')->get());
       
        return inertia('MachineType/Index',[
            'machinetypes'=> $machinetypes,
        ]);
    }


    public function create()

    {
        $plants = PlantResource::collection(Plant::all());
        return inertia('MachineType/Create',[
            'plants'=> $plants
        ]);

    }


    public function store(StoreMachineTypeRequest $request)
    
    {
        MachineType::create($request->validated());


        return redirect()->route('machinetypes.index');
    }

    public function edit(MachineType $machinetype)
    {
        $plants = PlantResource::collection(Plant::all());
        return inertia('MachineType/Edit',[
            'machinetype'=> new MachineTypeResource($machinetype->load('plant')),
            'plants' => $plants
        ]);
    }

    public function update(UpdateMachineTypeRequest $request, MachineType $machinetype)


    {

        $machinetype->update($request->validated());



        return redirect()->route('machinetypes.index');

    }


    public function destroy(MachineType $machinetype)

    {
        $machinetype->delete();


        return redirect ()->route('machinetypes.index');
    }

    
}
