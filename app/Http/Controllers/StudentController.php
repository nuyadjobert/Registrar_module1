<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Enrollment;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // GET /api/students
    public function index()
    {
        $students = Student::all();

        return response()->json([
            'message' => 'Students retrieved successfully',
            'data'    => $students
        ]);
    }

    // GET /api/students/{id}
    public function show($id)
    {
        $student = Student::findOrFail($id);

        return response()->json([
            'message' => 'Student retrieved successfully',
            'data'    => $student
        ]);
    }

    // GET /api/students/{id}/cor
    public function cor($id)
    {
        $student = Student::findOrFail($id);

        // Check if there's a released COR request
        $docRequest = DocumentRequest::where('student_id', $id)
                                     ->where('type', 'cor')
                                     ->where('status', 'released')
                                     ->first();

        if (!$docRequest) {
            return response()->json([
                'message' => 'COR is not yet available. Please ensure payment has been made and the document has been released.'
            ], 403);
        }

        $student->load(['enrollments' => function($query) {
            $query->where('status', 'approved')
                  ->with(['section.subject']);
        }]);

        $enrollments = $student->enrollments->map(function($enrollment) {
            return [
                'section' => $enrollment->section->section_name ?? null,
                'subject' => $enrollment->section->subject->subject_name ?? null,
                'units'   => $enrollment->section->subject->units ?? null,
                'status'  => $enrollment->status,
            ];
        });

        return response()->json([
            'message' => 'Certificate of Registration retrieved successfully',
            'data'    => [
                'student_number' => $student->student_number,
                'name'           => $student->name,
                'course'         => $student->course,
                'enrollments'    => $enrollments,
                'total_units'    => $enrollments->sum('units'),
            ]
        ]);
    }

    // GET /api/students/{id}/transcript
    public function transcript($id)
    {
        $student = Student::findOrFail($id);

        // Check if there's a released TOR request
        $docRequest = DocumentRequest::where('student_id', $id)
                                     ->where('type', 'tor')
                                     ->where('status', 'released')
                                     ->first();

        if (!$docRequest) {
            return response()->json([
                'message' => 'TOR is not yet available. Please ensure payment has been made and the document has been released.'
            ], 403);
        }

        $student->load(['enrollments' => function($query) {
            $query->where('status', 'approved')
                  ->with(['section.subject']);
        }]);

        $records = $student->enrollments->map(function($enrollment) {
            return [
                'section' => $enrollment->section->section_name ?? null,
                'subject' => $enrollment->section->subject->subject_name ?? null,
                'units'   => $enrollment->section->subject->units ?? null,
                'status'  => $enrollment->status,
            ];
        });

        return response()->json([
            'message' => 'Transcript of Records retrieved successfully',
            'data'    => [
                'student_number' => $student->student_number,
                'name'           => $student->name,
                'course'         => $student->course,
                'records'        => $records,
                'total_units'    => $records->sum('units'),
            ]
        ]);
    }
}