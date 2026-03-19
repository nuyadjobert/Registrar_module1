<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'section_name',
        'subject_id',
        'capacity',
        'school_year',
        'semester',
        'status',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}