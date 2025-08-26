<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MainOperation;
class Employee extends Model
{
    use HasFactory;


    protected $fillable = ['name','employee_code'];



public function mainOperations()
{
    return $this->hasMany(MainOperation::class);
}
}
