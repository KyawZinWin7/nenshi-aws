<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\CarbonInterval;

class SizingLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'start_time' => $this->start_time ? $this->start_time->timezone('Asia/Tokyo')->format('Y-m-d H:i:s') : null,
            'end_time' => $this->end_time ? $this->end_time->timezone('Asia/Tokyo')->format('Y-m-d H:i:s') : null,
            'worked_seconds' => $this->worked_seconds,
            'duration' => $this->worked_seconds,
            'duration_per_employee' => CarbonInterval::seconds($this->worked_seconds)
                ->cascade()
                ->format('%H:%I:%S'),

            'paused_seconds' => $this->paused_seconds,
            'paused_duration_per_employee' => CarbonInterval::seconds($this->paused_seconds)
                ->cascade()
                ->format('%H:%I:%S'),
            'last_start_time' => $this->last_start_time,
            
           
        ];
    }
}
