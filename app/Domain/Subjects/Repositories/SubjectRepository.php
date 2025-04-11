<?php
namespace App\Domain\Subjects\Repositories;

use App\Domain\Faculties\Models\Faculty;
use App\Domain\Subjects\Models\Subject;
use Illuminate\Database\Eloquent\Collection;

class SubjectRepository
{
    /**
     * @return Collection|array
     */
    public function getAllSubjects(): Collection|array
    {
        return Subject::query()
            ->get()
            ->sortBy('name');
    }
}
