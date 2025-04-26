<?php

namespace App\Domain\Lessons\Actions;

use App\Domain\Lessons\DTO\StoreLessonDTO;
use App\Domain\Lessons\DTO\UpdateLessonDTO;
use App\Domain\Lessons\Models\Lesson;
use Exception;
use Illuminate\Support\Facades\DB;

class UpdateLessonAction
{
    /**
     * @param UpdateLessonDTO $dto
     * @return Lesson
     * @throws Exception
     */
    public function execute(UpdateLessonDTO $dto): Lesson
    {
        DB::beginTransaction();
        try {
            $lesson = $dto->getLesson();
            $lesson->teacher_id = $dto->getTeacherId();
            $lesson->subject_id = $dto->getSubjectId();
            $lesson->title = $dto->getTitle();
            $lesson->url = $dto->getUrl();
            $lesson->date = $dto->getDate();
            $lesson->update();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();
        return $lesson;
    }
}
