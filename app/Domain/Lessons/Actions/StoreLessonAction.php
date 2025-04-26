<?php

namespace App\Domain\Lessons\Actions;

use App\Domain\Lessons\DTO\StoreLessonDTO;
use App\Domain\Lessons\Models\Lesson;
use Exception;
use Illuminate\Support\Facades\DB;

class StoreLessonAction
{
    /**
     * @param StoreLessonDTO $dto
     * @return array
     * @throws Exception
     */
    public function execute(StoreLessonDTO $dto): array
    {
        DB::beginTransaction();
        try {
            $data = array();
            foreach ($dto->getData() as $lessonData) {
                $lesson = new Lesson();
                $lesson->user_id = auth()->id();
                $lesson->teacher_id = $lessonData['teacher_id'];
                $lesson->subject_id = $lessonData['subject_id'];
                $lesson->title = $lessonData['title'];
                $lesson->url = $lessonData['url'];
                $lesson->date = $lessonData['date'];
                $lesson->save();
                $data[] = $lesson;
            }
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();
        return $data;
    }
}
