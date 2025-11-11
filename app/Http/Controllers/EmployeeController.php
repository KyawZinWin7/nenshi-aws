<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use App\Models\Department;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;

use App\Models\MainOperation;

class EmployeeController extends Controller
{
    
   public function index()
   {
        $employees = Employee::with('department')->get(); // <- get() လုပ်ရမယ်

        return inertia('Employees/Index', [
            'employees' => EmployeeResource::collection($employees),
        ]);
   }


     

    public function create()

    {
        $departments = DepartmentResource::collection(Department::all());
        return inertia('Employees/Create',[
            'departments' => $departments
        ]);

       
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
         $departments = DepartmentResource::collection(Department::all());
        
        
        return inertia('Employees/Edit',[
            'employee'=> new EmployeeResource($employee->load('department')),
            'departments' => $departments,
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
