<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionState extends Model
{
    protected $fillable = ['state','employee_id_number'];
}
