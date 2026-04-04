<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Mass Assignable
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'student_number',
        'name',
        'program_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Student belongs to a Program
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // Student has many Enrollments
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // Student has many Document Requests (COR, TOR)
    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    // Student → Sections (through Enrollments)
    public function sections()
    {
        return $this->belongsToMany(Section::class, 'enrollments')
            ->withPivot('status', 'payment_status', 'approved_by', 'approved_at')
            ->withTimestamps();
    }

    // Student → Subjects (through Sections)
    public function subjects()
    {
        return $this->hasManyThrough(
            Subject::class,
            Section::class,
            'id',          // Foreign key on sections table
            'id',          // Foreign key on subjects table
            'id',          // Local key on students table
            'subject_id'   // Local key on sections table
        );
    }

    // Student → Grades (for TOR)
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    // Example: "2023-0001 - Juan Dela Cruz"
    public function getDisplayNameAttribute()
    {
        return "{$this->student_number} - {$this->name}";
    }
}