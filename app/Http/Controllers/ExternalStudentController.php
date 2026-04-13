<?php

namespace App\Http\Controllers;

use App\Models\Student;
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
                [
                    'student_number' => $data['student_number']
                ],
                [
                    'first_name'   => $data['first_name'],
                    'last_name'    => $data['last_name'],
                    'email'        => $data['email'],
                    'phone_number' => $data['phone_number'] ?? null,
                    'course_id'    => $data['course_id'] ?? null,
                    'enrolled_at'  => $data['enrolled_at'] ?? null,
                ]
            );

            $synced++;
        }

        return response()->json([
            'message' => 'External students synced successfully',
            'total_received' => count($students),
            'total_synced'   => $synced
        ]);
    }
}