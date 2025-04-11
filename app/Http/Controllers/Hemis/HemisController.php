<?php

namespace App\Http\Controllers\Hemis;

use App\Domain\Departments\Repositories\DepartmentRepository;
use App\Domain\Departments\Resources\DepartmentResource;
use App\Domain\Faculties\Resources\FacultyResource;
use App\Domain\Subjects\Resources\SubjectResource;
use App\Domain\Faculties\Repositories\FacultyRepository;
use App\Domain\Subjects\Repositories\SubjectRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HemisController extends Controller
{
    /**
     * @var mixed|FacultyRepository
     */
    public mixed $faculties;

    /**
     * @var mixed|DepartmentRepository
     */
    public mixed $departments;

    /**
     * @var mixed|SubjectRepository
     */
    public mixed $subjects;

    /**
     * @param FacultyRepository $facultyRepository
     * @param DepartmentRepository $departmentRepository
     * @param SubjectRepository $subjectRepository
     */
    public function __construct(FacultyRepository $facultyRepository, DepartmentRepository $departmentRepository, SubjectRepository $subjectRepository)
    {
        $this->faculties = $facultyRepository;
        $this->departments = $departmentRepository;
        $this->subjects = $subjectRepository;
    }

    /**
     * @return JsonResponse
     */
    public function getAllFaculties()
    {
        return $this->successResponse('',FacultyResource::collection($this->faculties->getAllFaculties()));
    }

    /**
     * @param $facultyId
     * @return JsonResponse
     */
    public function getAllDepartments($facultyId)
    {
        return $this->successResponse('',DepartmentResource::collection($this->departments->getAllDepartments($facultyId)));
    }

    /**
     * @return JsonResponse
     */
    public function getAllSubjects()
    {
        return $this->successResponse('',SubjectResource::collection($this->subjects->getAllSubjects()));
    }
}
