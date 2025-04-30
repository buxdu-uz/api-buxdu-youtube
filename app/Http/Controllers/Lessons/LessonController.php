<?php

namespace App\Http\Controllers\Lessons;

use App\Domain\Lessons\Actions\StoreLessonAction;
use App\Domain\Lessons\Actions\UpdateLessonAction;
use App\Domain\Lessons\DTO\StoreLessonDTO;
use App\Domain\Lessons\DTO\UpdateLessonDTO;
use App\Domain\Lessons\Models\Lesson;
use App\Domain\Lessons\Repositories\LessonRepository;
use App\Domain\Lessons\Requests\LessonFilterRequest;
use App\Domain\Lessons\Requests\StoreLessonRequest;
use App\Domain\Lessons\Requests\UpdateLessonRequest;
use App\Domain\Lessons\Resources\LessonResource;
use App\Filters\LessonFilter;
use App\Http\Controllers\Controller;
use App\Imports\LessonImport;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;

class LessonController extends Controller
{
    /**
     * @var mixed|LessonRepository
     */
    public mixed $lessons;

    /**
     * @param LessonRepository $lessonRepository
     */
    public function __construct(LessonRepository $lessonRepository)
    {
        $this->lessons = $lessonRepository;
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return LessonResource::collection($this->lessons->paginate(\request()->query('pagination', 20)));
    }

    /**
     * @param StoreLessonRequest $request
     * @param StoreLessonAction $action
     * @return JsonResponse
     */
    public function store(StoreLessonRequest $request, StoreLessonAction $action)
    {
        try {
            $dto = StoreLessonDTO::fromArray($request->validated());
            $response = $action->execute($dto);

            return $this->successResponse('Lesson created.', LessonResource::collection($response));
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * @param UpdateLessonRequest $request
     * @param Lesson $lesson
     * @param UpdateLessonAction $action
     * @return JsonResponse
     */
    public function update(UpdateLessonRequest $request, Lesson $lesson, UpdateLessonAction $action)
    {
        try {
            $dto = UpdateLessonDTO::fromArray(array_merge($request->validated(), ['lesson' => $lesson]));
            $response = $action->execute($dto);

            return $this->successResponse('Lesson updated.', new LessonResource($response));
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }

    /**
     * @param Lesson $lesson
     * @return JsonResponse
     */
    public function destroy(Lesson $lesson)
    {
        try {
            $lesson->delete();
            return $this->successResponse('Lesson deleted.');
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }


//    MAIN
    public function groupByLesson(LessonFilterRequest $request)
    {
        $filter = app()->make(LessonFilter::class, ['queryParams' => array_filter($request->validated())]);
        $query = $this->lessons->mainLessons($filter);

        $totalCount = $query->count();
        $lessons = $query->paginate(\request()->query('pagination', 20));


        return LessonResource::collection($lessons)->additional([
            'status' => true,
            'lesson_count' => $totalCount,
        ]);
    }

    /**
     * @param Lesson $lesson
     * @return JsonResponse
     */
    public function show(Lesson $lesson)
    {
        return $this->successResponse('', new LessonResource($lesson));
    }


//    STATISTIKA

    //1 chi yol
//    public function statistics(Request $request)
//    {
//        $query = Lesson::query()->with('teacher.profile.department.faculty');
//
//        if ($request->filled('faculty_id')) {
//            $query->whereHas('teacher.profile.department', function ($q) use ($request) {
//                $q->where('faculty_id', $request->faculty_id);
//            });
//            $groupByKey = fn($lesson) => optional($lesson->teacher->profile->department)
//                ? $lesson->teacher->profile->department->id . '|' . $lesson->teacher->profile->department->name
//                : 'unknown|Unknown';
//        } elseif ($request->filled('department_id')) {
//            $query->whereHas('teacher.profile', function ($q) use ($request) {
//                $q->where('department_id', $request->department_id);
//            });
//            $groupByKey = fn($lesson) => optional($lesson->teacher)
//                ? $lesson->teacher->id . '|' . $lesson->teacher->full_name
//                : 'unknown|Unknown';
//        } else {
//            $groupByKey = fn($lesson) => optional($lesson->teacher->profile->department->faculty)
//                ? $lesson->teacher->profile->department->faculty->id . '|' . $lesson->teacher->profile->department->faculty->name
//                : 'unknown|Unknown';
//        }
//
//        $lessons = $query->get();
//
//        $result = $lessons
//            ->groupBy($groupByKey)
//            ->map(function ($group, $key) {
//                [$id, $name] = explode('|', $key);
//                return [
//                    'id' => (int) $id,
//                    'name' => $name,
//                    'count' => $group->count(),
//                ];
//            })
//            ->values();
//
//        return response()->json(['data' => $result]);
//    }

//2 chi yo'l
    public function statistics(Request $request)
    {
        $result = match (true) {
            $request->filled('faculty_id') => $this->getFacultyStatistics($request->faculty_id),
            $request->filled('department_id') => $this->getDepartmentStatistics($request->department_id),
            default => $this->getGeneralStatistics()
        };

        return response()->json(['data' => $result]);
    }

    protected function getFacultyStatistics($facultyId)
    {
        return Lesson::query()
            ->whereHas('teacher.profile.department', fn($q) => $q->where('faculty_id', $facultyId))
            ->with('teacher.profile.department')
            ->get()
            ->groupBy(fn($lesson) => $this->getDepartmentKey($lesson))
            ->map($this->mapGroupedItems())
            ->values();
    }

    protected function getDepartmentStatistics($departmentId)
    {
        return Lesson::query()
            ->whereHas('teacher.profile', fn($q) => $q->where('department_id', $departmentId))
            ->with('teacher')
            ->get()
            ->groupBy(fn($lesson) => $this->getTeacherKey($lesson))
            ->map($this->mapGroupedItems())
            ->values();
    }

    protected function getGeneralStatistics()
    {
        return Lesson::with('teacher.profile.department.faculty')
            ->get()
            ->groupBy(fn($lesson) => $this->getFacultyKey($lesson))
            ->map($this->mapGroupedItems())
            ->values();
    }

    protected function getDepartmentKey($lesson)
    {
        $department = optional($lesson->teacher->profile)->department;
        return $department ? "{$department->id}|{$department->name}" : '0|Unknown Department';
    }

    protected function getTeacherKey($lesson)
    {
        $teacher = $lesson->teacher;
        return $teacher ? "{$teacher->id}|{$teacher->full_name}" : '0|Unknown Teacher';
    }

    protected function getFacultyKey($lesson)
    {
        $faculty = optional($lesson->teacher->profile->department)->faculty;
        return $faculty ? "{$faculty->id}|{$faculty->name}" : '0|Unknown Faculty';
    }

    protected function mapGroupedItems()
    {
        return function ($group, $key) {
            [$id, $name] = explode('|', $key);
            return [
                'id' => (int)$id,
                'name' => $name,
                'count' => $group->count(),
            ];
        };
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240' // Up to 10MB
        ]);

        try {
            Excel::import(new LessonImport(), $request->file('file'));
            return $this->successResponse('Darslar muvaffaqiyatli import qilindi!');
        } catch (Exception $e) {
            return $this->errorResponse('Import paytida xato', $e->getMessage());
        }
    }
}
