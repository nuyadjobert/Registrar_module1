<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Enrollment;
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
        $student = Student::with(['enrollments' => function($query) {
            $query->where('status', 'approved')
                  ->with(['section.subject']);
        }])->findOrFail($id);

        $enrollments = $student->enrollments->map(function($enrollment) {
            return [
                'section'  => $enrollment->section->section_name ?? null,
                'subject'  => $enrollment->section->subject->subject_name ?? null,
                'units'    => $enrollment->section->subject->units ?? null,
                'status'   => $enrollment->status,
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
        $student = Student::with(['enrollments' => function($query) {
            $query->where('status', 'approved')
                  ->with(['section.subject']);
        }])->findOrFail($id);

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