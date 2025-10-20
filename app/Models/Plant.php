<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MachineType;
use App\Models\MachineTypePlant;

class Plant extends Model
{
    use HasFactory;


    protected $fillable = ['name'];


     public function machineTypes()
    {
        return $this->hasMany(MachineType::class);
    }

     public function machineTypePlants()
    {
        return $this->hasMany(MachineTypePlant::class);
    }

}
