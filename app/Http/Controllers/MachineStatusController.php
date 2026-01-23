<?php

namespace App\Http\Controllers;

use App\Http\Resources\MachineNumberResource;
use App\Http\Resources\PlantResource;
use App\Models\MachineNumber;
use App\Models\Plant;
use App\Models\SizingOperation;

class MachineStatusController extends Controller
{
    public function index()
    {
        $machines = MachineNumber::with([
            'machineTypePlant.machineType',
            'machineTypePlant.plant',
        ])->get();

        $machines->each(function ($machine) {

            $lastOp = SizingOperation::where('machine_number_id', $machine->id)
                ->with('task')
                ->latest()
                ->first();

            // no operation or not running
            if (! $lastOp || $lastOp->status !== 'running') {
                $machine->drive_status = 'stopped';

                return;
            }

            // running
            $machine->drive_status = match ($lastOp->task?->task_type) {
                'drive' => 'running',
                'repair' => 'repair',
                default => 'prepare', // prepare is default
            };
        });

        return inertia('MachineStatus/SMDashboard', [
            'machinenumbers' => MachineNumberResource::collection($machines),
            'plants' => PlantResource::collection(
                Plant::all() //all factories
            ),

        ]);
    }
}
