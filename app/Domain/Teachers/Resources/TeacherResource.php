<?php

namespace App\Domain\Teachers\Resources;

use App\Domain\Departments\Resources\DepartmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
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
            'full_name' => $this->full_name,
            'short_name' => $this->short_name,
            'employee_id_number' => $this->employee_id_number,
            'department' => new DepartmentResource($this->profile->department),
        ];
    }
}
