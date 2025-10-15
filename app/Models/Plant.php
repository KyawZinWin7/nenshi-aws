<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\MachineType;

class Plant extends Model
{
    use HasFactory;


    protected $fillable = ['name'];


     public function machineTypes()
    {
        return $this->hasMany(MachineType::class);
    }
}
