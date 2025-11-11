<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;

class DepartmentController extends Controller
{
    public function index()
    {

        $departments = DepartmentResource::collection(Department::all());
        return inertia('Departments/Index',[
            'departments'=> $departments,
        ]);
    }


    public function create()
    {
        return inertia('Departments/Create');
    }



    public function store(StoreDepartmentRequest $request)

    {
        Department::create($request->validated());
        return redirect()->route('departments.index');


    }

    public function edit(Department $department)
    {
        return inertia('Departments/Edit',[
        'department'=> DepartmentResource::make($department)
        ]) ;
    }


    public function update(UpdateDepartmentRequest $request, Department $department)

    {
        $department->update($request->validated());

        return redirect()->route('departments.index');
    }





     public function destroy(Department $department)

    {
        $department->delete();


        return redirect ()->route('departments.index');
    }

}
