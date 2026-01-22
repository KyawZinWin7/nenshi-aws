<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MainOperation;
use App\Models\MachineType;
use App\Models\Department;
use App\Models\SmallTask;

class Task extends Model
{
    use HasFactory;


    protected $fillable = ['name','machine_type_id','department_id','task_type'];


    public function mainOperations()
    {
        return $this->hasMany(MainOperation::class);
    }


    public function machineType()
    {
        return $this->belongsTo(MachineType::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function smallTasks()
    {
        return $this->hasMany(SmallTask::class);
    }
    public function sizingOperations()
    {
        return $this->hasMany(SizingOperation::class);
    }
}
