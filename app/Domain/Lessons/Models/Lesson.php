<?php

namespace App\Domain\Lessons\Models;

use App\Domain\Subjects\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'user_id',
        'teacher_id',
        'subject_id',
        'title',
        'url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
