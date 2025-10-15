<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineNumber extends Model
{
    protected $fillable = ['machine_type_id','number'];


     public function machineType()
        {
            return $this->belongsTo(MachineType::class);
        }
}
