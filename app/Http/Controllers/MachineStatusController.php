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

            // 1. no operation
            if (! $lastOp) {
                $machine->drive_status = 'stopped';

                return;
            }

            // 2. if running
            if ($lastOp->status === 'running') {
                $machine->drive_status = $lastOp->task?->is_drive_task
                    ? 'running'   // drive task
                    : 'prepare';  // non-drive task

                return;
            }

            // 3. if completed / paused
            $machine->drive_status = 'stopped';
        });

        return inertia('MachineStatus/SMDashboard', [
            'machinenumbers' => MachineNumberResource::collection($machines),
            'plants' => PlantResource::collection(
                Plant::where('id', 8)->orWhere('id', 9)->get()
            ),

        ]);
    }
}
