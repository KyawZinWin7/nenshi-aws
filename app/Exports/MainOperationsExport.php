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
        $query = MainOperation::query();

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }
        if (!empty($this->filters['employee_id'])) {
            $query->where('employee_id', $this->filters['employee_id']);
        }
        if (!empty($this->filters['machine_type_id'])) {
            $query->where('machine_type_id', $this->filters['machine_type_id']);
        }
        if (!empty($this->filters['machine_number'])) {
            $query->where('machine_number', $this->filters['machine_number']);
        }
        if (!empty($this->filters['task_id'])) {
            $query->where('task_id', $this->filters['task_id']);
        }
        $query->where('status', 1);
        return $query;
    }

    public function headings(): array
    {
        return [
            'Date', '機台', '機台の番号', '作業', '開始時間', '終了時間', '担当者', '合計時間'
        ];
    }

    public function map($mainOperation): array
    {
        return [
            $mainOperation->created_at?->timezone('Asia/Tokyo')->format('Y-m-d'),
            optional($mainOperation->machineType)->name ?? '',
            $mainOperation->machine_number,
            optional($mainOperation->task)->name ?? '',
            $mainOperation->start_time?->timezone('Asia/Tokyo')->format('H:i:s'),
            $mainOperation->end_time?->timezone('Asia/Tokyo')->format('H:i:s'),
            optional($mainOperation->employee)->name ?? '',
            $mainOperation->total_time ?? '',
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // တစ်ကြိမ် ၁၀၀၀ row စီ query
    }
}
