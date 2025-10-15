<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\MachineNumberResource;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\MachineNumber;

class MachineNumberController extends Controller
{
      public function index()
        {
            
            $machineNumbers = MachineNumberResource::collection(MachineNumber::with('machineType')->get());
            return inertia('MachineNumbers/Index',[
                'machineNumbers'=> $machineNumbers,
            ]);
        }
}
