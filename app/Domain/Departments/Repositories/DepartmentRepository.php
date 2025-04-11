<?php
namespace App\Domain\Departments\Repositories;

use App\Domain\Departments\Models\Department;
use App\Domain\Faculties\Models\Faculty;
use Illuminate\Database\Eloquent\Collection;

class DepartmentRepository
{
    /**
     * @param $faculty_id
     * @return Collection|array
     */
    public function getAllDepartments($faculty_id): Collection|array
    {
        return Department::query()
            ->where('faculty_id', $faculty_id)
            ->get()
            ->sortBy('name');
    }
}
