<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Support\Facades\Http;
use App\Models\Grade;

class StudentController extends Controller
{
    /**
     * List all students (for registrar viewing)
     */
    public function index()
    {
        $students = Student::with('program', 'enrollments.section.subject')->get();
        return response()->json($students);
    }

    /**
     * View a single student
     */
    public function show($id)
    {
        $student = Student::with('program', 'enrollments.section.subject', 'documentRequests')->findOrFail($id);
        return response()->json($student);
    }

    /**
     * Check if student has unpaid enrollments/fines
     * Returns true if there are unpaid items, false otherwise
     */
    protected function checkPayments(Student $student)
    {
        // If Cashier module is enabled, call its API
        if (env('CASHIER_ENABLED', false)) {
            try {
                $cashierApiUrl = env('CASHIER_API_URL') . "/api/payments/check-unpaid/{$student->id}";

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('CASHIER_API_KEY'),
                    'Accept' => 'application/json',
                ])->get($cashierApiUrl);

                if ($response->failed()) {
                    // API call failed, throw exception
                    throw new \Exception('Unable to verify payment status with cashier.');
                }

                $data = $response->json();
                // Assume API returns has_unpaid_fines = true/false
                return !empty($data['has_unpaid_fines']) && $data['has_unpaid_fines'] === true;

            } catch (\Exception $e) {
                // Handle errors gracefully
                abort(503, 'Payment verification service currently unavailable: ' . $e->getMessage());
            }
        }

        // Fallback: check local payment_status field
        $unpaid = $student->enrollments->where('payment_status', '!=', 'paid');
        return $unpaid->count() > 0;
    }

    /**
     * Get Certificate of Registration (COR)
     */
    public function cor($id)
    {
        $student = Student::with(['enrollments.section.subject', 'program'])->findOrFail($id);

        // Check payments
        if ($this->checkPayments($student)) {
            return response()->json([
                'message' => 'Cannot generate COR. Student has unpaid enrollments/fines.'
            ], 403);
        }

        $corData = [
            'student' => $student->name,
            'student_number' => $student->student_number,
            'program' => $student->program->name,
            'enrollments' => $student->enrollments->map(function ($enroll) {
                return [
                    'section' => $enroll->section->section_name,
                    'subject' => $enroll->section->subject->subject_name,
                    'units' => $enroll->section->subject->units,
                    'status' => $enroll->status,
                ];
            }),
        ];

        return response()->json([
            'message' => 'COR generated successfully',
            'data' => $corData
        ]);
    }

    /**
     * Get Transcript of Records (TOR)
     */
    public function transcript($id)
    {
        $student = Student::with(['enrollments.section.subject', 'enrollments.section.grades', 'program'])->findOrFail($id);

        // Check payments
        if ($this->checkPayments($student)) {
            return response()->json([
                'message' => 'Cannot generate TOR. Student has unpaid enrollments/fines.'
            ], 403);
        }

        $torData = [
            'student' => $student->name,
            'student_number' => $student->student_number,
            'program' => $student->program->name,
            'grades' => $student->enrollments->map(function ($enroll) {
                $grade = $enroll->section->grades->firstWhere('student_id', $enroll->student_id);
                return [
                    'subject' => $enroll->section->subject->subject_name,
                    'section' => $enroll->section->section_name,
                    'grade' => $grade?->grade ?? 'N/A',
                    'remarks' => $grade?->remarks ?? 'N/A',
                ];
            }),
        ];

        return response()->json([
            'message' => 'TOR generated successfully',
            'data' => $torData
        ]);
    }
}