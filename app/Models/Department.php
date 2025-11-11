<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MachineType;
use App\Models\Employee;
use App\Models\Task;

class Department extends Model
{
    use HasFactory;
    
    protected $fillable = ['name','department_code'];



     public function machineTypes()
    {
        return $this->hasMany(MachineType::class);
    }


     public function employees()
    {
        return $this->hasMany(Employee::class);
    }

     public function tasks()
    {
        return $this->hasMany(Task::class);
    }



}



