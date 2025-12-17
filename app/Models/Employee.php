<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; 
use App\Models\MainOperation;
use App\Models\Department;

class Employee extends Authenticatable 
{
    use HasFactory, Notifiable,HasRoles;

    protected $fillable = [
        'name',
        'employee_code',
        'password',
        'department_id',
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


    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function sizingOperations()
    {
        return $this->hasMany(SizingOperation::class);
    }


    public function sizingLogs()
    {
        return $this->hasMany(SizingLog::class);
    }
}
