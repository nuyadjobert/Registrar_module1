<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    // GET /api/enrollments
    public function index()
    {
        $enrollments = Enrollment::with(['student', 'section'])->get();

        return response()->json([
            'message' => 'Enrollments retrieved successfully',
            'data'    => $enrollments
        ]);
    }

    // GET /api/enrollments/{id}
    public function show($id)
    {
        $enrollment = Enrollment::with(['student', 'section'])->findOrFail($id);

        return response()->json([
            'message' => 'Enrollment retrieved successfully',
            'data'    => $enrollment
        ]);
    }

    // POST /api/enrollments/{id}/approve
    public function approve($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        if ($enrollment->status === 'approved') {
            return response()->json([
                'message' => 'Enrollment is already approved'
            ], 422);
        }

        // Payment check — powered by PaymentService stub for now
        if (!$this->paymentService->hasPaid($enrollment)) {
            return response()->json([
                'message' => 'Cannot approve enrollment. Student has not yet paid.'
            ], 422);
        }

        $enrollment->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Enrollment approved successfully',
            'data'    => $enrollment->load(['student', 'section'])
        ]);
    }

    // POST /api/enrollments/{id}/reject
    public function reject($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        if ($enrollment->status === 'rejected') {
            return response()->json([
                'message' => 'Enrollment is already rejected'
            ], 422);
        }

        $enrollment->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Enrollment rejected successfully',
            'data'    => $enrollment->load(['student', 'section'])
        ]);
    }

    // POST /api/enrollments/{id}/mark-paid  ← cashier team calls this
    public function markAsPaid($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        if ($enrollment->payment_status === 'paid') {
            return response()->json([
                'message' => 'Enrollment is already marked as paid'
            ], 422);
        }

        $enrollment->update(['payment_status' => 'paid']);

        return response()->json([
            'message' => 'Payment status updated successfully',
            'data'    => $enrollment->load(['student', 'section'])
        ]);
    }
}