<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Task;
use App\Models\MachineType;

class SmallTask extends Model
{
    protected $fillable = ['name','machine_type_id'];



    public function machineType()
    {
        return $this->belongsTo(MachineType::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function mainOperations()
    {
        return $this->hasMany(MainOperation::class);
    }
    public function sizingOperations()
    {
        return $this->hasMany(SizingOperation::class);
    }
}
