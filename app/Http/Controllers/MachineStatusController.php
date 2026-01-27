<?php

namespace App\Http\Controllers;

use App\Http\Resources\MachineNumberResource;
use App\Http\Resources\PlantResource;
use App\Models\MachineNumber;
use App\Models\Plant;
use Illuminate\Http\Request;

class MachineStatusController extends Controller
{
    public function index(Request $request)
{
    $plantId = $request->input('plant_id')
        ?? Plant::orderBy('id')->value('id');

    $machines = MachineNumber::with([
            'machineTypePlant.machineType',
            'machineTypePlant.plant',
            'sizingOperations' => function ($q) {
                $q->latest()->limit(1);
            },
            'sizingOperations.task',
        ])
        ->whereHas('machineTypePlant', function ($q) use ($plantId) {
            $q->where('plant_id', $plantId);
        })
        ->get();

    // drive status
    $machines->each(function ($machine) {
        $lastOp = $machine->sizingOperations->first();

        if (! $lastOp || $lastOp->status !== 'running') {
            $machine->drive_status = 'stopped';
            return;
        }

        $machine->drive_status = match ($lastOp->task?->task_type) {
            'drive'  => 'running',
            'repair' => 'repair',
            default  => 'prepare',
        };
    });

    $grouped = $machines
        ->groupBy(fn ($m) => $m->machineTypePlant->plant->name)
        ->map(fn ($plantMachines) =>
            $plantMachines
                ->groupBy(fn ($m) => $m->machineTypePlant->machineType->name)
                ->map(fn ($machines) =>
                    $machines->map(fn ($m) => [
                        'machine_id' => $m->id,
                        'type'       => $m->machineTypePlant->machineType->name,
                        'number'     => $m->number,
                        'status'     => $m->drive_status,
                    ])->values()
                )
        );

    return inertia('MachineStatus/SMDashboard', [
        'machinenumbers' => $grouped,
        'plants' => PlantResource::collection(
            Plant::orderBy('id')->get()
        ),
        'filters' => [
            'plant_id' => $plantId, // ← Vue default sync
        ],
    ]);
}

}
