<?php

namespace App\Http\Resources;

use App\Domain\Departments\Resources\DepartmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'employee_id_number' => $this->employee_id_number,
            'full_name' => $this->full_name,
            'short_name' => $this->short_name,
            'avatar' => $this->profile->avatar,
            'role' => $this->getRoleNames(),
            'department' => new DepartmentResource($this->profile->department),
        ];
    }
}
