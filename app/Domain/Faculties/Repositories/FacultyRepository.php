<?php
namespace App\Domain\Faculties\Repositories;

use App\Domain\Faculties\Models\Faculty;
use Illuminate\Database\Eloquent\Collection;

class FacultyRepository
{
    /**
     * @return Collection|array
     */
    public function getAllFaculties(): Collection|array
    {
        return Faculty::query()
            ->get()
            ->sortBy('name');
    }
}
