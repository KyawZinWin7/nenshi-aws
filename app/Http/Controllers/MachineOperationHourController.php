<?php

namespace App\Http\Controllers;

use App\Models\MachineNumber;
use App\Models\Plant;
use App\Models\SizingOperation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MachineOperationHourController extends Controller
{
    
    public function getSizingMachine(Request $request)
    {
        //  month (already existing)
        $month = $request->input('month', now()->format('Y-m'));

        //  plant filter (NEW)
        $plantId = $request->input('plant_id');

        $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $monthEnd = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        /**
         *  summary (month filter only) ← existing logic
         */
        $summary = SizingOperation::select(
            'machine_number_id',

            DB::raw("
            SUM(
                CASE
                    WHEN task_id IN (SELECT id FROM tasks WHERE task_type = 'drive')
                    THEN worked_seconds
                    ELSE 0
                END
            ) AS running_seconds
        "),

            DB::raw("
            SUM(
                CASE
                    WHEN task_id IN (SELECT id FROM tasks WHERE task_type = 'prepare')
                    THEN worked_seconds
                    ELSE 0
                END
            ) AS setup_seconds
        "),

            DB::raw("
            SUM(
                CASE
                    WHEN task_id IN (SELECT id FROM tasks WHERE task_type = 'repair')
                    THEN worked_seconds
                    ELSE 0
                END
            ) AS repair_seconds
        ")
        )
            ->whereBetween('start_time', [$monthStart, $monthEnd])
            ->groupBy('machine_number_id');

        /**
         *  machine master + plant filter
         */
        $plantId = $request->input('plant_id');
        $machinenumbers = MachineNumber::query()
            ->when(!empty($plantId), function ($q) use ($plantId) {
                $q->whereHas('machineTypePlant', function ($q) use ($plantId) {
                    $q->where('plant_id', $plantId);
                });
            })
            ->leftJoinSub($summary, 's', function ($join) {
                $join->on('machine_numbers.id', '=', 's.machine_number_id');
            })
            ->leftJoin('machine_type_plants', 'machine_numbers.machine_type_plant_id', '=', 'machine_type_plants.id')
            ->leftJoin('machine_types', 'machine_type_plants.machine_type_id', '=', 'machine_types.id')
            ->leftJoin('plants', 'machine_type_plants.plant_id', '=', 'plants.id')
            ->select(
                'plants.name as plant_name',
                'machine_types.name as machine_type_name',
                'machine_numbers.number',
                DB::raw('COALESCE(s.running_seconds, 0) as running_seconds'),
                DB::raw('COALESCE(s.setup_seconds, 0) as setup_seconds'),
                DB::raw('COALESCE(s.repair_seconds, 0) as repair_seconds')
            )
            ->orderBy('plants.name')
            ->orderBy('machine_types.name')
            ->orderBy('machine_numbers.number')
            ->get();

        return inertia('MachineTimeSummary/SizingMachine', [
            'machinenumbers' => $machinenumbers,
            'month' => $month,
            'plants' => Plant::select('id', 'name')->get(),
            'selectedPlant' => $plantId !== null ? (string)$plantId : '',
        ]);
    }
}
