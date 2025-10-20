<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MainOperationResource extends JsonResource
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
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'members' => EmployeeResource::collection($this->whenLoaded('members')), // ✅ add this line

            // direct fields
            'machine_number' => $this->machineNumber ? new MachineNumberResource($this->whenLoaded('machineNumber')) : null,
            // 'start_time' => $this->start_time,
        
            // 'end_time' => $this->end_time,
            'start_time' => $this->start_time ? $this->start_time->timezone('Asia/Tokyo')->format('Y-m-d H:i:s') : null,
            'end_time' => $this->end_time ? $this->end_time->timezone('Asia/Tokyo')->format('Y-m-d H:i:s') : null,

            'total_time' => $this->total_time,
            'status' => (int) $this->status,
            // 'created_at' => optional($this->created_at)->toDateString(),
            'created_at' => $this->created_at->timezone('Asia/Tokyo')->format('Y-m-d'),

        //    'created_at' => optional($this->created_at)->toDateTimeString(),
        


        ];
    }
}
