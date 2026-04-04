<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */
    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const PAYMENT_UNPAID = 'unpaid';
    const PAYMENT_PAID   = 'paid';
    const PAYMENT_PARTIAL = 'partial';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'student_id',
        'section_id',
        'status',          // pending, approved, rejected
        'payment_status',  // unpaid, paid, partial
        'approved_by',     // admin/instructor ID
        'approved_at',     // timestamp
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Enrollment belongs to a Student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Enrollment belongs to a Section
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // Enrollment approver (optional)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by'); // assuming admin users
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    // Approved enrollments
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    // Pending enrollments
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // Rejected enrollments
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
}