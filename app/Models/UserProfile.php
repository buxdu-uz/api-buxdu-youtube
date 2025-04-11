<?php

namespace App\Models;

use App\Domain\Departments\Models\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = ['user_id','department_id','avatar'];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
