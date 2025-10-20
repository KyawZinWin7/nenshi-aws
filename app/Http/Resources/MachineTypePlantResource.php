<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MachineTypePlantResource extends JsonResource
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
            'machine_type_id' => new MachineTypeResource($this->whenLoaded('machineType')),
            'plant_id' => new PlantResource($this->whenLoaded('plant')),
            // 'machine_type_id' => $this->machine_type_id,
            // 'plant_id' => $this->plant_id,
            'start_number' => $this->start_number,
            'end_number' => $this->end_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
