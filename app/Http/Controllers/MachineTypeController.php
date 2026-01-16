<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MachineType;
use App\Models\MainOperation;
use App\Models\Plant;
use App\Http\Resources\MachineTypeResource;
use App\Models\Department;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\StoreMachineTypeRequest;
use App\Http\Requests\UpdateMachineTypeRequest;
use App\Http\Resources\PlantResource;
use App\Models\Task;
class MachineTypeController extends Controller
{
    


    public function index()
    {
        $machinetypes = MachineType::with('department')->get();
        
       
        return inertia('MachineType/Index',[
            'machinetypes' => MachineTypeResource::collection($machinetypes),
        ]);
    }


    public function create()

    {
        $departments = DepartmentResource::collection(Department::all());
        
        return inertia('MachineType/Create',[
            'departments' => $departments,
        ]);

    }


    public function store(StoreMachineTypeRequest $request)
    
    {

        // Create machine type

        $machinetype = MachineType::create($request->validated());



        //Auto Create 運転　task


        Task::create([
            'machine_type_id' => $machinetype->id,
            'name' => '運転',
            'department_id' => $machinetype->department_id,
            'is_drive_task' => true,

        ]);


        return redirect()->route('machinetypes.index')
        ->with('success','Machine type created with 運転 task');
    }

    public function edit(MachineType $machinetype)
    {
        $departments = DepartmentResource::collection(Department::all());
        
        return inertia('MachineType/Edit',[
            'machinetype'=> new MachineTypeResource($machinetype->load('department')),
            'departments' => $departments
            
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
