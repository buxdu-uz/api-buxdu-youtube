<?php

namespace App\Http\Controllers\Lessons;

use App\Domain\Lessons\Actions\StoreLessonAction;
use App\Domain\Lessons\DTO\StoreLessonDTO;
use App\Domain\Lessons\Models\Lesson;
use App\Domain\Lessons\Repositories\LessonRepository;
use App\Domain\Lessons\Requests\StoreLessonRequest;
use App\Domain\Lessons\Resources\LessonResource;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function index()
    {
        return LessonResource::collection($this->lessons->paginate(\request()->query('pagination', 20)));
    }

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


//    MAIN
    public function groupByLesson($faculty_id)
    {
        $lessons = Lesson::with('teacher.profile.department')
            ->whereHas('teacher.profile.department', function ($query) use ($faculty_id) {
                $query->where('faculty_id', $faculty_id);
            })
            ->get();

        // Group by department name
        $grouped = $lessons->groupBy(function ($lesson) {
            return $lesson->teacher->profile->department->name ?? 'Unknown Department';
        });

        // Transform into a new collection with counts
        $groupedData = $grouped->map(function ($items, $departmentName) {
            return [
                'department' => $departmentName,
                'lesson_count' => $items->count(),
                'lessons' => LessonResource::collection($items->values()), // remove keys from collection
            ];
        })->values(); // reset keys for pagination

        // Paginate the grouped results manually
        $perPage = 10;
        $page = request()->get('page', 1);
        $pagedData = new LengthAwarePaginator(
            $groupedData->forPage($page, $perPage),
            $groupedData->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return response()->json($pagedData);

    }
}
