<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MainOperation;
use App\Models\Plant;
use App\Models\Task;
use App\Models\MachineNumber;
use App\Models\MachineTypePlant;
use App\Models\Department;
use App\Models\SmallTask;

class MachineType extends Model
{
    use HasFactory;

    protected $fillable = ['name','department_id'];

    public function mainOperations()
    {
        return $this->hasMany(MainOperation::class);
    }

     public function machineNumbers()
    {
        return $this->hasMany(MachineNumber::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
     public function machineTypePlants()
    {
        return $this->hasMany(MachineTypePlant::class);
    }

    public function plants()
    {
        return $this->belongsToMany(Plant::class, 'machine_type_plants')
                    ->withPivot('start_number', 'end_number')
                    ->withTimestamps();
    }


     public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function samllTasks()
    {
        return $this->hasMany(SmallTask::class);
    }

    public function sizingOperations()
    {
        return $this->hasMany(SizingOperation::class);
    }
    

}
