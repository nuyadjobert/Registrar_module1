<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'section_name',
        'subject_id',
        'instructor_id',
        'term_id',     // ✅ replaced school_year + semester
        'capacity',
        'schedule',
        'room',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Section belongs to a Subject
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    // Section belongs to an Instructor
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    // Section belongs to a Term
    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    // Section has many Enrollments
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // Section → Students (through enrollments)
    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments')
            ->withPivot('status', 'payment_status', 'approved_by', 'approved_at')
            ->withTimestamps();
    }

    // Section → Grades
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    // Example: "BSIT-1A - OOP"
    public function getDisplayNameAttribute()
    {
        return "{$this->section_name} - {$this->subject->subject_name}";
    }
}