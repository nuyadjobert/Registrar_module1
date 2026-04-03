<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    protected $fillable = [
        'student_id',
        'type',
        'payment_status',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}