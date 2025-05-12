<?php

namespace App\Domain\Departments\Resources;

use App\Domain\Faculties\Repositories\FacultyRepository;
use App\Domain\Faculties\Resources\FacultyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'lesson_count' => $this->lesson_count,
            'faculty' => new FacultyResource($this->faculty),
        ];
    }
}
