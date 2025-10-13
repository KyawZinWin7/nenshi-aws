<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;
use App\Models\MainOperation;

class Employee extends Authenticatable 
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'employee_code',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function mainOperations()
    {
        return $this->hasMany(MainOperation::class);
    }
}
