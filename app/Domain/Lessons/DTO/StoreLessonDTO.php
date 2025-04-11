<?php

namespace App\Domain\Lessons\DTO;

class StoreLessonDTO
{
    /**
     * @var array
     */
    private array $data;

    /**
     * @param array $data
     * @return StoreLessonDTO
     */
    public static function fromArray(array $data): StoreLessonDTO
    {
        $dto = new self();
        $dto->setData($data['data']);

        return $dto;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
