<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $table = 'curricula';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'program_id',   // links to Program
        'subject_id',   // links to Subject
        'year_level',   // 1, 2, 3, 4
        'semester',     // 1, 2, Summer
        'school_year',  // e.g., 2025-2026
        'status',       // active, inactive
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Curriculum belongs to a Program
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // Curriculum belongs to a Subject
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    // Only active curriculum entries
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}