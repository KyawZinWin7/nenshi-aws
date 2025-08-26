<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Http\Resources\EmployeeResource;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;

use App\Models\MainOperation;

class EmployeeController extends Controller
{
    
     public function index()
    {

        $employees = EmployeeResource::collection(Employee::all());
        // dd($machinetypes);
        return inertia('Employees/Index',[
            'employees'=> $employees,
        ]);
    }

    public function create()

    {
        return inertia('Employees/Create');
    }


     public function store(StoreEmployeeRequest $request)
    
    {
        Employee::create($request->validated());


        return redirect()->route('employees.index');
    }



     public function edit(Employee $employee)
    {
        return inertia('Employees/Edit',[
            'employee'=> EmployeeResource::make($employee)
        ]);
    }




     public function update(UpdateEmployeeRequest $request, Employee $employee)


    {

        $employee->update($request->validated());



        return redirect()->route('employees.index');

    }


     public function destroy(Employee $employee)

    {
        $employee->delete();


        return redirect ()->route('employees.index');
    }



}
