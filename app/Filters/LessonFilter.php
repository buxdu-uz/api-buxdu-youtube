<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use JetBrains\PhpStorm\ArrayShape;

class LessonFilter extends AbstractFilter
{
    public const FACULTY_ID = 'faculty_id';

    public const DEPARTMENT_ID = 'department_id';

    public const SUBJECT_ID = 'subject_id';

    public const TITLE = 'title';

    /**
     * @return array[]
     */
    #[ArrayShape([self::FACULTY_ID => "array", self::DEPARTMENT_ID => "array", self::SUBJECT_ID => "array", self::TITLE => "array"])] protected function getCallbacks(): array
    {
        return [
            self::FACULTY_ID => [$this, 'faculty_id'],
            self::DEPARTMENT_ID => [$this, 'department_id'],
            self::SUBJECT_ID => [$this, 'subject_id'],
            self::TITLE => [$this, 'title'],
        ];
    }

    public function department_id(Builder $builder, $value): void
    {
        $builder->whereHas('teacher', function ($query) use ($value) {
            $query->whereHas('profile',function($subQuery) use ($value) {
                $subQuery->where('department_id', $value);
            });
        });
    }

    public function faculty_id(Builder $builder, $value): void
    {
        $builder->whereHas('teacher.profile.department', function ($query) use ($value) {
            $query->where('faculty_id', $value);
        });
    }

    public function subject_id(Builder $builder, $value): void
    {
        $builder->where('subject_id',$value);
    }

    public function title(Builder $builder, $value): void
    {
        $builder->where('title','like','%'.$value.'%');
    }

}
