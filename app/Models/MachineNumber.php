<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MachineTypePlant;

class MachineNumber extends Model
{
    protected $fillable = ['machine_type_plant_id','number', 'control_type', 'auto_stop_hours'];


     public function machineTypePlant()
        {
            return $this->belongsTo(MachineTypePlant::class);
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
