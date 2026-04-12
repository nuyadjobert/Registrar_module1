<?php

namespace App\Http\Controllers;

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
     * Approve enrollment - only allow if payment is paid
     */
    public function approve($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        // VALIDATION: Check if payment is paid
        if ($enrollment->payment_status !== Enrollment::PAYMENT_PAID) {
            return response()->json([
                'message' => 'Cannot approve enrollment. Student payment status is ' . $enrollment->payment_status . '. Payment must be marked as paid before approval.',
                'payment_status' => $enrollment->payment_status
            ], 403);
        }

        // Optional: Toggle feature flag to enable cashier API check for additional validation
        if (env('CASHIER_ENABLED', false)) {
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
     * Show a single enrollment
     */
    public function show($id)
    {
        $enrollment = Enrollment::with('student.program', 'section.subject', 'section.instructor')->findOrFail($id);
        return response()->json($enrollment);
    }
}