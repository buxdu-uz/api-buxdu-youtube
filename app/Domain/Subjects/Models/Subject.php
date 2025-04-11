<?php

namespace App\Domain\Subjects\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
   protected $fillable = ['code','name'];
}
