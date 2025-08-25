<?php

namespace App\Exports;

use App\Models\MainOperation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MainOperationsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = MainOperation::query();

        // フィルターがある場合のみクエリに追加
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

        return $query->get();
    }

    // Excel ヘッダー
    public function headings(): array
    {
        return [
            'Date', '機台', '機台の番号', '作業', '開始時間', '終了時間', '担当者', '合計時間'
        ];
    }

    // 各行の map
   public function map($mainOperation): array
{
    return [
        $mainOperation->created_at->timezone('Asia/Tokyo')->format('Y-m-d'),  // Date
        optional($mainOperation->machineType)->name ?? '',                   // 機台
        $mainOperation->machine_number,                                      // 機台の番号
        optional($mainOperation->task)->name ?? '',                          // 作業
        $mainOperation->start_time ? $mainOperation->start_time->timezone('Asia/Tokyo')->format('H:i:s') : '',
        $mainOperation->end_time ? $mainOperation->end_time->timezone('Asia/Tokyo')->format('H:i:s') : '',
        optional($mainOperation->employee)->name ?? '',                      // 担当者
        $mainOperation->total_time ?? '',                                    // 合計時間
    ];
}

}
