<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; 
use App\Models\MainOperation;

class Employee extends Authenticatable 
{
    use HasFactory, Notifiable,HasRoles;

    protected $fillable = [
        'name',
        'employee_code',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $guard_name = 'web';

    // public function mainOperations()
    // {
    //     return $this->hasMany(MainOperation::class);
    // }


    public function mainOperations()
{
    return $this->belongsToMany(MainOperation::class, 'main_operation_member', 'employee_id', 'main_operation_id')
                ->withTimestamps();
}

}
