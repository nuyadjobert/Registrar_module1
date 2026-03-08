<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'student_number',
        'name',
        'course'
    ];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}