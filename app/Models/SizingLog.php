<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizingLog extends Model
{
    protected $fillable = [
        'sizing_operation_id',
        'employee_id',
        'start_time',
        'end_time',
        'worked_seconds',
        'last_start_time',
        'paused_time',
        'paused_seconds',
    ];

    protected $casts = [
    'start_time' => 'datetime',
    'end_time' => 'datetime',
    'paused_time' => 'datetime',
    'last_start_time' => 'datetime',
];



    public function sizingOperation()
    {
        return $this->belongsTo(SizingOperation::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
