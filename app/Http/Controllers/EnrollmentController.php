<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class EnrollmentController extends Controller
{
    /**
     * List all enrollments
     */
    public function index()
    {
        $enrollments = Enrollment::with('student.program', 'section.subject', 'section.instructor')->get();
        return response()->json($enrollments);
    }

    /**
     * Enroll a student into a section
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $request->student_id,
            'section_id' => $request->section_id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        return response()->json($enrollment, 201);
    }

    /**
     * Approve enrollment with cashier payment check
     */
    public function approve($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        // Toggle feature flag to enable cashier API check
        if (!env('CASHIER_ENABLED', false)) {
            // If cashier is disabled, approve directly (for dev/testing)
            $enrollment->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            return response()->json([
                'message' => 'Enrollment approved (payment check skipped)',
                'enrollment' => $enrollment
            ]);
        }

        // Call Cashier API to check for unpaid fines/payments
        try {
            $cashierApiUrl = env('CASHIER_API_URL') . "/api/payments/check-unpaid/{$enrollment->student_id}";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('CASHIER_API_KEY'),
                'Accept' => 'application/json',
            ])->get($cashierApiUrl);

            if ($response->failed()) {
                return response()->json([
                    'message' => 'Unable to verify payment status with cashier. Try again later.'
                ], 503);
            }

            $data = $response->json();

            // Assume cashier API returns JSON like: { "has_unpaid_fines": true/false }
            if (!empty($data['has_unpaid_fines']) && $data['has_unpaid_fines'] === true) {
                return response()->json([
                    'message' => 'Cannot approve enrollment. Student has unpaid fines or fees.'
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Payment verification service currently unavailable.',
                'error' => $e->getMessage(),
            ], 503);
        }

        // All checks passed, approve enrollment
        $enrollment->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Enrollment approved successfully',
            'enrollment' => $enrollment
        ]);
    }

    /**
     * Reject enrollment
     */
    public function reject($id, Request $request)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->update([
            'status' => 'rejected',
            'approved_by' => auth()->id() ?? null,
            'approved_at' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Enrollment rejected successfully', 'enrollment' => $enrollment]);
    }

    /**
     * Show a single enrollment
     */
    public function show($id)
    {
        $enrollment = Enrollment::with('student.program', 'section.subject', 'section.instructor')->findOrFail($id);
        return response()->json($enrollment);
    }
}