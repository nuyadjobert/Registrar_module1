<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StudentController extends Controller
{
    /**
     * List all students
     */
    public function index()
    {
        $students = Student::with([
            'program',
            'enrollments.section.subject'
        ])->get();

        return response()->json($students);
    }

    /**
     * View a single student
     */
    public function show($id)
    {
        $student = Student::with([
            'program',
            'enrollments.section.subject',
            'documentRequests'
        ])->findOrFail($id);

        return response()->json($student);
    }

    /**
     * Get grades for a student
     * Pulls from enrollments → section → grades filtered by student_id
     */
    public function grades($id)
    {
        $student = Student::with([
            'enrollments.section.subject',
            'enrollments.section.grades' => function ($query) use ($id) {
                $query->where('student_id', $id);
            }
        ])->findOrFail($id);

        $grades = $student->enrollments->map(function ($enrollment) use ($id) {
            $grade = $enrollment->section->grades
                ->firstWhere('student_id', $id);

            return [
                'id'       => $grade?->id ?? $enrollment->id,
                'section'  => [
                    'id'      => $enrollment->section->id,
                    'name'    => $enrollment->section->section_name,
                    'subject' => [
                        'id'   => $enrollment->section->subject->id   ?? null,
                        'code' => $enrollment->section->subject->subject_code ?? 'N/A',
                        'name' => $enrollment->section->subject->subject_name ?? 'N/A',
                    ]
                ],
                'grade'    => $grade?->grade   ?? 'N/A',
                'remarks'  => $grade?->remarks ?? 'N/A',
            ];
        })->filter(fn($g) => $g !== null)->values();

        return response()->json($grades);
    }

    /**
     * Add a grade for a student
     */
    public function addGrade(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'section_id' => 'required|exists:sections,id',
            'grade'      => 'required',
            'remarks'    => 'nullable|string|max:255',
        ]);

        $grade = Grade::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'section_id' => $request->section_id,
            ],
            [
                'grade'   => $request->grade,
                'remarks' => $request->remarks ?? null,
            ]
        );

        return response()->json([
            'message' => 'Grade saved successfully.',
            'data'    => $grade
        ], 201);
    }

    /**
     * Check if student has unpaid enrollments/fines
     */
    protected function checkPayments(Student $student)
    {
        if (env('CASHIER_ENABLED', false)) {
            try {
                $cashierApiUrl = env('CASHIER_API_URL') . "/api/payments/check-unpaid/{$student->id}";

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('CASHIER_API_KEY'),
                    'Accept'        => 'application/json',
                ])->get($cashierApiUrl);

                if ($response->failed()) {
                    throw new \Exception('Unable to verify payment status with cashier.');
                }

                $data = $response->json();
                return !empty($data['has_unpaid_fines']) && $data['has_unpaid_fines'] === true;

            } catch (\Exception $e) {
                abort(503, 'Payment verification service currently unavailable: ' . $e->getMessage());
            }
        }

        $unpaid = $student->enrollments->where('payment_status', '!=', 'paid');
        return $unpaid->count() > 0;
    }

    /**
     * Get Certificate of Registration (COR)
     */
    public function cor($id)
    {
        $student = Student::with([
            'enrollments.section.subject',
            'program'
        ])->findOrFail($id);

        if ($this->checkPayments($student)) {
            return response()->json([
                'message' => 'Cannot generate COR. Student has unpaid enrollments or fines.'
            ], 403);
        }

        $corData = [
            'student'        => $student->name,
            'student_number' => $student->student_number,
            'program'        => $student->program->name,
            'enrollments'    => $student->enrollments->map(function ($enroll) {
                return [
                    'section' => $enroll->section->section_name,
                    'subject' => $enroll->section->subject->subject_name,
                    'units'   => $enroll->section->subject->units,
                    'status'  => $enroll->status,
                ];
            }),
        ];

        return response()->json([
            'message' => 'COR generated successfully',
            'data'    => $corData
        ]);
    }

    /**
     * Get Transcript of Records (TOR)
     */
    public function transcript($id)
    {
        $student = Student::with([
            'enrollments.section.subject',
            'enrollments.section.grades',
            'program'
        ])->findOrFail($id);

        if ($this->checkPayments($student)) {
            return response()->json([
                'message' => 'Cannot generate TOR. Student has unpaid enrollments or fines.'
            ], 403);
        }

        $torData = [
            'student'        => $student->name,
            'student_number' => $student->student_number,
            'program'        => $student->program->name,
            'grades'         => $student->enrollments->map(function ($enroll) {
                $grade = $enroll->section->grades
                    ->firstWhere('student_id', $enroll->student_id);
                return [
                    'subject' => $enroll->section->subject->subject_name,
                    'section' => $enroll->section->section_name,
                    'grade'   => $grade?->grade   ?? 'N/A',
                    'remarks' => $grade?->remarks ?? 'N/A',
                ];
            }),
        ];

        return response()->json([
            'message' => 'TOR generated successfully',
            'data'    => $torData
        ]);
    }
}