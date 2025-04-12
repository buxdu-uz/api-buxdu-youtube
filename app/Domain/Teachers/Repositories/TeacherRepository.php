<?php
namespace App\Domain\Teachers\Repositories;

use App\Models\User;

class TeacherRepository
{
    public function getAllTeachers($department_id)
    {
        return User::query()
            ->role('teacher')
            ->whereHas('profile', function ($query) use ($department_id) {
                $query->where('department_id', $department_id);
            })
            ->orderBy('full_name') // sort in DB instead of collection
            ->get();
    }
}
