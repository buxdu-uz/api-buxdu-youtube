<?php

namespace App\Http\Controllers\Teachers;

use App\Domain\Teachers\Repositories\TeacherRepository;
use App\Domain\Teachers\Resources\TeacherResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public mixed $teachers;

    public function __construct(TeacherRepository $teacherRepository)
    {
        $this->teachers = $teacherRepository;
    }

    public function getAllTeacher($department_id)
    {
        return $this->successResponse('', TeacherResource::collection($this->teachers->getAllTeachers($department_id)));
    }
}
