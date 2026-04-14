<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Support\Facades\Http;

class ExternalStudentController extends Controller
{
    /**
     * Sync students from external API
     */
    public function syncStudents()
    {
        $apiUrl = "https://admission-api-production.up.railway.app/api/external/students";
        $apiKey = "uGz1oXUDVNVIq1xWmmLglKqgYd6eEP1gy55uIjvwe4a6Lw84FBPETQLmbQzkXtSF";

        $response = Http::withHeaders([
            'Accept'  => 'application/json',
            'api_key' => $apiKey,
        ])->get($apiUrl);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Failed to fetch external students',
                'error'   => $response->body()
            ], 500);
        }

        $students = $response->json('data');

        if (!$students) {
            return response()->json([
                'message' => 'No students found in external API'
            ], 404);
        }

        $synced = 0;

        foreach ($students as $data) {
            Student::updateOrCreate(
                ['student_number' => $data['student_number']],
                [
                    'name'         => trim($data['first_name'] . ' ' . $data['last_name']),
                    'program_id'   => $data['course_id'],
                    'first_name'   => $data['first_name'],
                    'last_name'    => $data['last_name'],
                    'email'        => $data['email'],
                    'phone_number' => $data['phone_number'] ?? null,
                    'enrolled_at'  => $data['enrolled_at'] ?? null,
                ]
            );
            $synced++;
        }

        return response()->json([
            'message'        => 'External students synced successfully',
            'total_received' => count($students),
            'total_synced'   => $synced
        ]);
    }

    /**
     * Get all documents for a specific student
     */
    public function getDocuments($studentNumber)
    {
        $student = Student::where('student_number', $studentNumber)->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student not found with number: ' . $studentNumber
            ], 404);
        }

        $documents = StudentDocument::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'student' => [
                'id'             => $student->id,
                'student_number' => $student->student_number,
                'name'           => $student->name,
            ],
            'documents' => $documents
        ]);
    }

    /**
     * Get specific document type for a student
     * Example: GET /api/external/students/STU-2026-0001/documents/tor
     */
    public function getDocumentByType($studentNumber, $type)
    {
        $student = Student::where('student_number', $studentNumber)->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student not found'
            ], 404);
        }

        $document = StudentDocument::where('student_id', $student->id)
            ->where('document_type', $type)
            ->latest()
            ->first();

        if (!$document) {
            return response()->json([
                'message' => "No document of type '{$type}' found for student {$studentNumber}"
            ], 404);
        }

        return response()->json([
            'student_number' => $student->student_number,
            'document_type'  => $document->document_type,
            'file_path'      => $document->file_path,
            'file_url'       => asset('storage/' . $document->file_path),   // Adjust if using different storage
            'uploaded_at'    => $document->created_at->format('Y-m-d H:i:s'),
        ]);
    }
}