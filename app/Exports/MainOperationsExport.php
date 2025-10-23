<?php

namespace App\Exports;

use App\Models\MainOperation;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class MainOperationsExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = MainOperation::query()
            ->with(['plant', 'machineType', 'machineNumber', 'task', 'employee', 'members'])
            ->where('status', 1);

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['plant_id'])) {
            $query->where('plant_id', $this->filters['plant_id']);
        }

        if (!empty($this->filters['employee_id'])) {
            $query->where('employee_id', $this->filters['employee_id']);
        }

        if (!empty($this->filters['machine_type_id'])) {
            $query->where('machine_type_id', $this->filters['machine_type_id']);
        }

        if (!empty($this->filters['machine_number'])) {
            $number = (int) $this->filters['machine_number'];
            $query->whereHas('machineNumber', function ($q) use ($number) {
                $q->where('number', $number);
            });
        }

        if (!empty($this->filters['task_id'])) {
            $query->where('task_id', $this->filters['task_id']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            '日付', '工場', '機台', '機台の番号', '作業', '開始時間', '終了時間', '担当者', '担当メンバー', '合計時間'
        ];
    }

    public function map($mainOperation): array
    {
        return [
            $mainOperation->created_at?->timezone('Asia/Tokyo')->format('Y-m-d'),
            optional($mainOperation->plant)->name ?? '',
            optional($mainOperation->machineType)->name ?? '',
            optional($mainOperation->machineNumber)->number ?? '',
            optional($mainOperation->task)->name ?? '',
            $mainOperation->start_time?->timezone('Asia/Tokyo')->format('H:i:s'),
            $mainOperation->end_time?->timezone('Asia/Tokyo')->format('H:i:s'),
            optional($mainOperation->employee)->name ?? '',
            $mainOperation->members->pluck('name')->join(', '),
            $mainOperation->total_time ?? '',
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
