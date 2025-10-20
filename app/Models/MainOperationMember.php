<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainOperationMember extends Model
{
     // Pivot table အတွက် အခြေခံ model
    protected $table = 'main_operation_members';

    protected $fillable = [
        'main_operation_id',
        'employee_id',
    ];
}
