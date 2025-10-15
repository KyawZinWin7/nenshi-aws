<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MainOperation;
use App\Models\MachineType;

class Task extends Model
{
    use HasFactory;


    protected $fillable = ['name','machine_type_id'];


    public function mainOperations()
    {
        return $this->hasMany(MainOperation::class);
    }


public function machineType()
    {
        return $this->belongsTo(MachineType::class);
    }
}
