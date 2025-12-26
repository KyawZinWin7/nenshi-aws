<?php

namespace App\Http\Controllers;

use App\Models\SizingLog;
use Carbon\Carbon;

class SizingLogController extends Controller
{
    public function complete($id)
    {
        $log = SizingLog::findOrFail($id);
        $now = Carbon::now('Asia/Tokyo');

        // already completed guard
        if ($log->end_time) {
            return back();
        }

        $workedSeconds = $log->worked_seconds;

        // if currently running → add last segment
        if ($log->last_start_time) {
            $start = Carbon::parse($log->last_start_time, 'Asia/Tokyo');

            if ($start->lte($now)) {
                $workedSeconds += $start->diffInSeconds($now);
            }
        }

        $log->update([
            'end_time' => $now,
            'worked_seconds' => $workedSeconds,
            'last_start_time' => null,
        ]);

        return back()->with('success', '担当者の作業が完了しました');
    }
}
