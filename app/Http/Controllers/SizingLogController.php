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


    public function stop(SizingLog $log)
    {
        $now = Carbon::now('Asia/Tokyo');

        // already stopped or completed guard
        if (!$log->last_start_time || $log->end_time) {
            return back();
        }

        $start = Carbon::parse($log->last_start_time, 'Asia/Tokyo');

        $workedSeconds = $log->worked_seconds;

        if ($start->lte($now)) {
            $workedSeconds += $start->diffInSeconds($now);
        }

        $log->update([
            'paused_time' => $now,
            'worked_seconds' => $workedSeconds,
            'last_start_time' => null,
        ]);

        return back()->with('success', '担当者の作業が一時停止されました');
    }


    public function resume(SizingLog $log)
    {
        $now = Carbon::now('Asia/Tokyo');

        // already running or completed guard
        if ($log->last_start_time || $log->end_time) {
            return back();
        }

        $log->update([
            'paused_seconds' => $log->paused_seconds + Carbon::parse($log->paused_time, 'Asia/Tokyo')->diffInSeconds($now),
            'last_start_time' => $now,
            'paused_time' => null,
        ]);

        return back()->with('success', '担当者の作業が再開されました');
    }
    public function destroy($id)
    {
        $log = SizingLog::findOrFail($id);

        // Prevent deletion if the log is completed
        if ($log->end_time) {
            return back()->with('error', '完了した作業ログは削除できません。');
        }

        $log->delete();

        return back()->with('success', '作業ログが削除されました。');
    }
}
