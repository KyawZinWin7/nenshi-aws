<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'duration' => $this->duration,
        ];
    }
}
