<?php

namespace App\Console\Commands;

use App\Http\Controllers\SizingOperationController;
use App\Models\SizingOperation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoCompleteSizingOperations extends Command
{
    protected $signature = 'sizing:auto-complete';

    protected $description = 'Auto complete sizing operations when auto stop hours exceeded';

    public function handle()
    {
        $now = Carbon::now('Asia/Tokyo');

        $operations = SizingOperation::where('status', 'running')
            ->whereHas('machineNumber', function ($q) {
                $q->where('control_type', 'auto')
                    ->whereNotNull('auto_stop_hours');
            })
            ->with('machineNumber')
            ->get();

        $controller = app(SizingOperationController::class);

        foreach ($operations as $op) {

            // must be actually running
            if (! $op->last_start_time) {
                continue;
            }

            $machine = $op->machineNumber;

            $elapsedSeconds = Carbon::parse(
                $op->last_start_time,
                'Asia/Tokyo'
            )->diffInSeconds($now);

            $limitSeconds = $machine->auto_stop_hours * 3600;

            if ($elapsedSeconds < $limitSeconds) {
                continue;
            }

            // reuse manual complete logic
            $controller->complete($op->id);

            $this->info("✔ Auto completed operation ID: {$op->id}");
        }

        return Command::SUCCESS;
    }
}
