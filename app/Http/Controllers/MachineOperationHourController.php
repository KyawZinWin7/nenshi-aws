<?php

namespace App\Http\Controllers;

use App\Models\MachineNumber;
use App\Models\SizingOperation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MachineOperationHourController extends Controller
{
    public function hgetSizingMachine(Request $request)
    {
        // --- summary per machine_number_id ---
        $summary = SizingOperation::select(
            DB::raw('DATE(start_time) as work_date'),
            'machine_number_id',

            // 運転
            DB::raw("
        SUM(
            CASE
                WHEN task_id IN (SELECT id FROM tasks WHERE task_type = 'drive')
                THEN worked_seconds
                ELSE 0
            END
        ) AS running_seconds
        "),

            // 準備
            DB::raw("
            SUM(
                CASE
                    WHEN task_id IN (SELECT id FROM tasks WHERE task_type = 'prepare')
                    THEN worked_seconds
                    ELSE 0
                END
            ) AS setup_seconds
        "),

            // 修理 ⭐️ここがポイント
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
            ->where('department_id', 2)
            ->groupBy(
                DB::raw('DATE(start_time)'),
                'machine_number_id'
            );

        // --- machine list ---
        $machinenumbers = MachineNumber::whereHas(
            'machineTypePlant.machineType',
            fn ($q) => $q->where('department_id', 2)
        )
            ->leftJoinSub($summary, 's', function ($join) {
                $join->on('machine_numbers.id', '=', 's.machine_number_id');
            })
            ->leftJoin('machine_type_plants', 'machine_numbers.machine_type_plant_id', '=', 'machine_type_plants.id')
            ->leftJoin('machine_types', 'machine_type_plants.machine_type_id', '=', 'machine_types.id')
            ->leftJoin('plants', 'machine_type_plants.plant_id', '=', 'plants.id')
            ->select(
                'machine_numbers.id',
                'machine_numbers.number',
                'plants.name as plant_name',
                'machine_types.name as machine_type_name',
                DB::raw('COALESCE(s.running_seconds,0) as running_seconds'),
                DB::raw('COALESCE(s.setup_seconds,0) as setup_seconds'),
                // DB::raw('COALESCE(s.stopped_seconds,0) as stopped_seconds'),
                DB::raw('COALESCE(s.repair_seconds,0) as repair_seconds')
            )
            ->orderBy('plants.name')
            ->orderBy('machine_types.name')
            ->orderBy('machine_numbers.number')
            ->get();

        return inertia('MachineTimeSummary/SizingMachine', [
            'machinenumbers' => $machinenumbers,
        ]);
    }

    //fix version
    public function fixgetSizingMachine(Request $request)
    {
        // 📅 選択された月（なければ今月）
        $month = $request->input('month', now()->format('Y-m'));

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $summary = SizingOperation::select(
            DB::raw('DATE(start_time) as work_date'),
            'machine_number_id',

            // 運転
            DB::raw("
            SUM(
                CASE
                    WHEN task_id IN (SELECT id FROM tasks WHERE task_type = 'drive')
                    THEN worked_seconds
                    ELSE 0
                END
            ) AS running_seconds
        "),

            // 準備
            DB::raw("
                SUM(
                    CASE
                        WHEN task_id IN (SELECT id FROM tasks WHERE task_type = 'prepare')
                        THEN worked_seconds
                        ELSE 0
                    END
                ) AS setup_seconds
            "),

            // 修理
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
            ->whereBetween('start_time', [$start, $end])
            ->where('department_id', 2)
            ->groupBy(
                DB::raw('DATE(start_time)'),
                'machine_number_id'
            );
        $machinenumbers = MachineNumber::whereHas(
            'machineTypePlant.machineType',
            fn ($q) => $q->where('department_id', 2)
        )->leftJoinSub($summary, 's', function ($join) {
            $join->on('machine_numbers.id', '=', 's.machine_number_id');
        })
            ->leftJoin('machine_type_plants', 'machine_numbers.machine_type_plant_id', '=', 'machine_type_plants.id')
            ->leftJoin('machine_types', 'machine_type_plants.machine_type_id', '=', 'machine_types.id')
            ->leftJoin('plants', 'machine_type_plants.plant_id', '=', 'plants.id')
            ->select(
                's.work_date',
                'plants.name as plant_name',
                'machine_types.name as machine_type_name',
                'machine_numbers.number',
                DB::raw('COALESCE(s.running_seconds,0) as running_seconds'),
                DB::raw('COALESCE(s.setup_seconds,0) as setup_seconds'),
                DB::raw('COALESCE(s.repair_seconds,0) as repair_seconds')
            )
            ->orderBy('plants.name')
            ->orderBy('machine_types.name')
            ->orderBy('machine_numbers.number')

            ->get();

        return inertia('MachineTimeSummary/SizingMachine', [
            'machinenumbers' => $machinenumbers,
            'month' => $month,
        ]);
    }

    //fix version by my month

    public function getSizingMachine(Request $request)
    {
        // 📅 選択された月（なければ今月）
        $month = $request->input('month', now()->format('Y-m'));

        $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $monthEnd = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        /**
         * 🔹 月と overlap する operation だけ取得
         */
        $summary = SizingOperation::select(
            'machine_number_id',

            // 🚗 運転
            DB::raw("
                SUM(
                    CASE
                        WHEN task_id IN (SELECT id FROM tasks WHERE task_type = 'drive')
                        THEN worked_seconds
                        ELSE 0
                    END
                ) AS running_seconds
            "),

            // 🧰 準備
            DB::raw("
                SUM(
                    CASE
                        WHEN task_id IN (SELECT id FROM tasks WHERE task_type = 'prepare')
                        THEN worked_seconds
                        ELSE 0
                    END
                ) AS setup_seconds
            "),

            // 🔧 修理 ✅（paused 含まれない）
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
            ->where('department_id', 2)
            ->groupBy('machine_number_id');

        /**
         * 🔹 機械マスタと JOIN
         */
        $machinenumbers = MachineNumber::whereHas(
            'machineTypePlant.machineType',
            fn ($q) => $q->where('department_id', 2)
        )
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
        ]);
    }
}
