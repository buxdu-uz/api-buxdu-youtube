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
        return Department::with('user_profile.user.lessons')
            ->where('faculty_id', $faculty_id)
            ->get()
            ->map(function ($department) {
                $lessonCount = $department->user_profile
                    ->flatMap(function ($profile) {
                        return optional($profile->user)->lessons ?? collect();
                    })
                    ->count();

                $department->lesson_count = $lessonCount;

                return $department;
            })
            ->sortBy('name');;
    }
}
