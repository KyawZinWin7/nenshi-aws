<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MainOperationMemberResource extends JsonResource
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
            'main_operation_id' => $this->main_operation_id,
            'employee_id' => $this->employee_id,

            // related data (optional)
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'main_operation' => new MainOperationResource($this->whenLoaded('mainOperation')),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
