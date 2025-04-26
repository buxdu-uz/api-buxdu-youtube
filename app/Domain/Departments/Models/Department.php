<?php

namespace App\Domain\Departments\Models;

use App\Domain\Faculties\Models\Faculty;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    protected $fillable = ['faculty_id','name','code'];

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function user_profile()
    {
        return $this->hasMany(UserProfile::class);
    }
}
