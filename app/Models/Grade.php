<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'student_id',
        'section_id',
        'grade',
        'remarks', // e.g., "Passed", "Failed"
    ];

    // Grade belongs to a Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Grade belongs to a Section
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // Easy access to Subject through Section
    public function subject()
    {
        return $this->hasOneThrough(
            Subject::class,
            Section::class,
            'id',          // Foreign key on sections
            'id',          // Foreign key on subjects
            'section_id',  // Local key on grades
            'subject_id'   // Local key on sections
        );
    }
}