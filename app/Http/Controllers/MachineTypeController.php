<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MachineType;
use App\Models\MainOperation;
use App\Http\Resources\MachineTypeResource;
use App\Http\Requests\StoreMachineTypeRequest;
use App\Http\Requests\UpdateMachineTypeRequest;

class MachineTypeController extends Controller
{
    


    public function index()
    {

        $machinetypes = MachineTypeResource::collection(MachineType::all());
        // dd($machinetypes);
        return inertia('MachineType/Index',[
            'machinetypes'=> $machinetypes,
        ]);
    }


    public function create()

    {
        return inertia('MachineType/Create');
    }


    public function store(StoreMachineTypeRequest $request)
    
    {
        MachineType::create($request->validated());


        return redirect()->route('machinetypes.index');
    }

    public function edit(MachineType $machinetype)
    {
        return inertia('MachineType/Edit',[
            'machinetype'=> MachineTypeResource::make($machinetype)
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
