<?php

namespace App\Domain\Lessons\Resources;

use App\Domain\Subjects\Resources\SubjectResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

class MainLessonResource extends JsonResource
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
            'title' => $this->title,
            'url' => $this->url,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'teacher' => $this->teacher,
            'subject' => new SubjectResource($this->subject),
        ];
    }
}
