<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    protected $fillable = [
        'name',       // e.g., "1st Sem 2026"
        'start_date',
        'end_date',
        'is_active',
    ];

    // Term has many Sections
    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}