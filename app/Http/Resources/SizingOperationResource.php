<?php

namespace App\Http\Resources;

use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SizingOperationResource extends JsonResource
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
            // relations
            'plant' => new PlantResource($this->whenLoaded('plant')),
            'machine_type' => new MachineTypeResource($this->whenLoaded('machineType')),
            'task' => new TaskResource($this->whenLoaded('task')),
            'small_task' => new SmallTaskResource($this->whenLoaded('smallTask')),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'sizinglogs' => SizingLogResource::collection($this->whenLoaded('sizingLogs')),

            // direct fields
            // 'machine_number' => $this->machineNumber ? new MachineNumberResource($this->whenLoaded('machineNumber')) : null,
            'machine_number' => new MachineNumberResource(
                $this->whenLoaded('machineNumber')
            ),

            'start_time' => $this->start_time ? $this->start_time->timezone('Asia/Tokyo')->format('H:i:s') : null,
            'end_time' => $this->end_time ? $this->end_time->timezone('Asia/Tokyo')->format('Y-m-d H:i:s') : null,

            'total_time' => $this->total_time,
            'status' => $this->status,
            'worked_seconds' => $this->worked_seconds,
            'worked_time' => CarbonInterval::seconds($this->worked_seconds)
                ->cascade()
                ->format('%H:%I:%S'),
            'paused_seconds' => $this->paused_seconds,
            'paused_seconds_hour' => CarbonInterval::seconds($this->paused_seconds)
                ->cascade()
                ->format('%H:%I:%S'),
            'last_start_time' => $this->last_start_time,
            'created_at' => $this->created_at->timezone('Asia/Tokyo')->format('Y-m-d'),
        ];
    }
}
