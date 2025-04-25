<?php

namespace App\Domain\Lessons\DTO;

use App\Domain\Lessons\Models\Lesson;

class UpdateLessonDTO
{
    /**
     * @var int
     */
    private int $teacher_id;

    /**
     * @var int
     */
    private int $subject_id;

    /**
     * @var string
     */
    private string $title;

    /**
     * @var string
     */
    private string $url;

    /**
     * @var Lesson
     */
    private Lesson $lesson;

    /**
     * @param array $data
     * @return UpdateLessonDTO
     */
    public static function fromArray(array $data): UpdateLessonDTO
    {
        $dto = new self();
        $dto->setTeacherId($data['teacher_id']);
        $dto->setSubjectId($data['subject_id']);
        $dto->setTitle($data['title']);
        $dto->setUrl($data['url']);
        $dto->setLesson($data['lesson']);

        return $dto;
    }

    /**
     * @return int
     */
    public function getTeacherId(): int
    {
        return $this->teacher_id;
    }

    /**
     * @param int $teacher_id
     */
    public function setTeacherId(int $teacher_id): void
    {
        $this->teacher_id = $teacher_id;
    }

    /**
     * @return int
     */
    public function getSubjectId(): int
    {
        return $this->subject_id;
    }

    /**
     * @param int $subject_id
     */
    public function setSubjectId(int $subject_id): void
    {
        $this->subject_id = $subject_id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return Lesson
     */
    public function getLesson(): Lesson
    {
        return $this->lesson;
    }

    /**
     * @param Lesson $lesson
     */
    public function setLesson(Lesson $lesson): void
    {
        $this->lesson = $lesson;
    }
}
