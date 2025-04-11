<?php

namespace App\Domain\Lessons\Repositories;

use App\Domain\Lessons\Models\Lesson;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class LessonRepository
{
    /**
     * @param $pagination
     * @return LengthAwarePaginator
     */
    public function paginate($pagination): LengthAwarePaginator
    {
        return Lesson::query()
            ->where(function ($query){
                if (Auth::check() && Auth::user()->hasRole('teacher')) {
                    $query->where('teacher_id', Auth::id());
                }
            })
            ->orderByDesc('created_at')
            ->paginate($pagination);
    }
}
