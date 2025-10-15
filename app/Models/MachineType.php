<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MainOperation;
use App\Models\Plant;
use App\Models\Task;
use App\Models\MachineNumber;

class MachineType extends Model
{
    use HasFactory;

    protected $fillable = ['name','plant_id'];


    public function mainOperations()
    {
        return $this->hasMany(MainOperation::class);
    }

     public function machineNumbers()
    {
        return $this->hasMany(MachineNumber::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
     public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}
