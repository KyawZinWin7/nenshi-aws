<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Plant;
use App\Models\MachineType;
use App\Models\MachineNumber;

class MachineTypePlant extends Model
{
     protected $fillable = ['machine_type_id', 'plant_id', 'start_number', 'end_number'];

    public function machineType()
    {
        return $this->belongsTo(MachineType::class);
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function machineNumbers()
    {
        return $this->hasMany(MachineNumber::class);
    }
}
