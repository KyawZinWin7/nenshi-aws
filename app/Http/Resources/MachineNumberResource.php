<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MachineNumberResource extends JsonResource
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
            'number' => $this->number,
            'machine_type_plant_id' => new MachineTypePlantResource($this->whenLoaded('machineTypePlant')),
            'drive_status' => $this->drive_status ?? 'stopped',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
