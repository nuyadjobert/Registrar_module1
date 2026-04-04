<?php

namespace App\Services;

use App\Models\Enrollment;

class PaymentService
{
    /**
     * Check if the student has paid for the given enrollment.
     * TODO: Replace with actual cashier module integration later.
     */
    public function hasPaid(Enrollment $enrollment): bool
    {
        return $enrollment->payment_status === 'paid';
    }
}