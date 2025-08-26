<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\Task;
use App\Models\MachineType;

class MainOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_type_id',
        'machine_number',
        'task_id',
        'start_time',
        'end_time',
        'total_time',
        'employee_id',
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

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
