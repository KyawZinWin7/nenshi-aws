<?php

namespace App\Exports;

use App\Models\SizingOperation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class SizingOperationsExport implements FromCollection, WithEvents, WithHeadings
{
    protected $filters;

    protected $rowStyles = [];

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $rows = collect();
        $currentRow = 2;
        $colorIndex = 0;

        $colors = ['F4B6B6', 'C9B97E', 'BFE5EE'];

        $query = SizingOperation::query();

        $query->with([
            'plant',
            'machineType',
            'machineNumber',
            'task',
            'sizingLogs',
            'sizingLogs.employee',
        ]);

        // 🔴 parent filter (critical)
        if (! empty($this->filters['employee_id'])) {
            $query->whereHas('sizingLogs', function ($q) {
                $q->where('employee_id', $this->filters['employee_id']);
            });
        }

        if (! empty($this->filters['date_from'])) {
            $query->whereDate('start_time', '>=', $this->filters['date_from']);
        }

        if (! empty($this->filters['date_to'])) {
            $query->whereDate('start_time', '<=', $this->filters['date_to']);
        }

        if (! empty($this->filters['plant_id'])) {
            $query->where('plant_id', $this->filters['plant_id']);
        }

        if (! empty($this->filters['machine_type_id'])) {
            $query->where('machine_type_id', $this->filters['machine_type_id']);
        }

        if (! empty($this->filters['machine_number'])) {
            $number = (int) $this->filters['machine_number'];
            $query->whereHas('machineNumber', fn ($q) => $q->where('number', $number));
        }

        $operations = $query->orderBy('start_time')->get();

        foreach ($operations as $op) {

            

            $color = $colors[$colorIndex % count($colors)];
            $colorIndex++;

            // 🔴 Task header
            $rows->push([
                $op->start_time?->format('Y-m-d'),
                $op->plant?->name,
                $op->machineType?->name,
                $op->machineNumber?->number,
                $op->task?->name,
                $op->start_time?->format('H:i:s'),
                $op->end_time?->format('H:i:s'),
                gmdate('H:i:s', $op->paused_seconds),
                $op->total_time,
            ]);

            $this->rowStyles[] = [
                'row' => $currentRow,
                'color' => $color,
                'bold' => true,
            ];
            $currentRow++;

            // 🟠 employee header
            $rows->push(['担当者', '', '', '', '', '開始', '終了', '時間停止', '合計時間']);
            $this->rowStyles[] = [
                'row' => $currentRow,
                'color' => $color,
                'bold' => true,
            ];
            $currentRow++;

            // 🔵 employee rows
            foreach ($op->sizingLogs as $log) {
                $rows->push([
                    $log->employee?->name,
                    '', '', '', '',
                    $log->start_time?->format('H:i:s'),
                    $log->end_time?->format('H:i:s'),
                    gmdate('H:i:s', $log->paused_seconds),
                    gmdate('H:i:s', $log->worked_seconds),
                ]);

                $this->rowStyles[] = [
                    'row' => $currentRow,
                    'color' => $color,
                    'bold' => false,
                ];
                $currentRow++;
            }

            $rows->push(['', '', '', '', '', '', '', '', '']);
            $currentRow++;
        }

        // ⭐⭐ THIS LINE FIXES YOUR ERROR ⭐⭐
        return $rows ?? collect();
    }

    public function headings(): array
    {
        return [
            '日付', '工場', '機台', '機号', '作業',
            '開始', '終了', '時間停止', '合計時間',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                foreach ($this->rowStyles as $style) {
                    $event->sheet->getStyle("A{$style['row']}:I{$style['row']}")
                        ->applyFromArray([
                            'fill' => [
                                'fillType' => 'solid',
                                'color' => ['rgb' => $style['color']],
                            ],
                            'font' => [
                                'bold' => $style['bold'],
                            ],
                        ]);
                }

                // Header bold
                $event->sheet->getStyle('A1:I1')->getFont()->setBold(true);

                // Auto width
                foreach (range('A', 'I') as $col) {
                    $event->sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
