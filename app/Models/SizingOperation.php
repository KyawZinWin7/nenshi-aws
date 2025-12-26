<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizingOperation extends Model
{
    protected $fillable = [
        'plant_id',
        'machine_type_id',
        'machine_number_id',
        'task_id',
        'small_task_id',
        'employee_id',
        'department_id',
        'start_time',
        'end_time',
        'total_time',
        'worked_seconds',
        'last_start_time',
        'description',
        'status',
    ];



    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function machineType()
    {
        return $this->belongsTo(MachineType::class);
    }

    public function machineNumber()
    {
        return $this->belongsTo(MachineNumber::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function smallTask()
    {
        return $this->belongsTo(SmallTask::class);
    }

    public function sizingLogs()
    {
        return $this->hasMany(SizingLog::class);
    }
    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
