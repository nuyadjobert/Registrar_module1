<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'code',        // e.g., BSIT
        'name',        // e.g., Bachelor of Science in Information Technology
        'department',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // One Program has many Students
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // One Program has many Curriculum entries
    public function curricula()
    {
        return $this->hasMany(Curriculum::class);
    }

    // Program has many Subjects through Curriculum
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'curricula')
            ->withPivot('year_level', 'semester', 'school_year', 'status')
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    // Custom display (e.g., "BSIT - Bachelor of Science in IT")
    public function getDisplayNameAttribute()
    {
        return "{$this->code} - {$this->name}";
    }
}