<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'document_type',   // e.g., 'tor', 'cor', 'psa', 'form_137', etc.
        'file_path',
        'original_name',
        'mime_type',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}