<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MainOperation;

class Task extends Model
{
    use HasFactory;


    protected $fillable = ['name'];


    public function mainOperations()
{
    return $this->hasMany(MainOperation::class);
}
}
