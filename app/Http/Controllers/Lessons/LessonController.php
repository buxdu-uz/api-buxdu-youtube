<?php

namespace App\Http\Controllers\Lessons;

use App\Domain\Lessons\Actions\StoreLessonAction;
use App\Domain\Lessons\DTO\StoreLessonDTO;
use App\Domain\Lessons\Models\Lesson;
use App\Domain\Lessons\Repositories\LessonRepository;
use App\Domain\Lessons\Requests\LessonFilterRequest;
use App\Domain\Lessons\Requests\StoreLessonRequest;
use App\Domain\Lessons\Resources\LessonResource;
use App\Filters\LessonFilter;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
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
    public function groupByLesson(LessonFilterRequest $request)
    {
        $filter = app()->make(LessonFilter::class,['queryParams' => array_filter($request->validated())]);
        $query = $this->lessons->mainLessons($filter);

        $totalCount = $query->count();
        $lessons = $query->paginate(\request()->query('pagination',20));


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
        return $this->successResponse('',new LessonResource($lesson));
    }
}
