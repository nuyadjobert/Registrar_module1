<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    protected $fillable = [
        'student_id',
        'type',          // e.g., "COR", "TOR"
        'payment_status',// unpaid, paid
        'status',        // pending, approved, completed
    ];

    // Belongs to a Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}