<?php

namespace App\Http\Controllers\Lessons;

use App\Domain\Lessons\Actions\StoreLessonAction;
use App\Domain\Lessons\DTO\StoreLessonDTO;
use App\Domain\Lessons\Repositories\LessonRepository;
use App\Domain\Lessons\Requests\StoreLessonRequest;
use App\Domain\Lessons\Resources\LessonResource;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

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
}
