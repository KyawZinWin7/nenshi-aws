<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\Task;
use App\Models\MachineType;
use App\Models\MachineNumber;
use App\Models\Department;

class MainOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'plant_id',
        'machine_type_id',
        'machine_number_id',
        'task_id',
        'start_time',
        'end_time',
        'total_time',
        'employee_id',
        'department_id',
        'small_task',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

     // Relationships

    public function machineType()
    {
        return $this->belongsTo(MachineType::class);
    }

    //  public function machineNumber()
    // {
    //     return $this->belongsTo(MachineNumber::class);
    // }


     public function machineNumber()
    {
        return $this->belongsTo(MachineNumber::class, 'machine_number_id');
    }


    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

     public function department()
    {
        return $this->belongsTo(Department::class);
    }

    
    // public function mainOperations()
    // {
    //     return $this->hasMany(MainOperation::class);
    // }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function members()
    {
        return $this->belongsToMany(
            Employee::class, 
            'main_operation_members', // ✅ correct table name
            'main_operation_id', 
            'employee_id'
        )->withTimestamps();
    }

    
}
