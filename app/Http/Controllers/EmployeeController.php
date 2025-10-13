<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $data = $request->validated();

     
        $data['password'] = Hash::make($data['password']);

         Employee::create($data);

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

         $data = $request->validated();

            // password ထည့်လျှင်သာ hash ပြန်လုပ်မယ်
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']); // မပြောင်းချင်လို့ password ကို skip
            }

            $employee->update($data);

            return redirect()->route('employees.index')->with('success', '更新しました');

    }


     public function destroy(Employee $employee)

    {
        $employee->delete();


        return redirect ()->route('employees.index');
    }



}
