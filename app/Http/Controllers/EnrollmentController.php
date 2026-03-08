<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    // GET /api/enrollments
    public function index()
    {
        $enrollments = Enrollment::with(['student', 'section'])->get();

        return response()->json([
            'message' => 'Enrollments retrieved successfully',
            'data'    => $enrollments
        ]);
    }

    // POST /api/enrollments
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        // Check if already enrolled
        $existing = Enrollment::where('student_id', $validated['student_id'])
                              ->where('section_id', $validated['section_id'])
                              ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Student is already enrolled in this section'
            ], 422);
        }

        $enrollment = Enrollment::create([
            'student_id' => $validated['student_id'],
            'section_id' => $validated['section_id'],
            'status'     => 'pending'
        ]);

        return response()->json([
            'message' => 'Enrollment created successfully',
            'data'    => $enrollment->load(['student', 'section'])
        ], 201);
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

    // PUT /api/enrollments/{id}
    public function update(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'sometimes|exists:students,id',
            'section_id' => 'sometimes|exists:sections,id',
            'status'     => 'sometimes|in:pending,approved,rejected',
        ]);

        $enrollment->update($validated);

        return response()->json([
            'message' => 'Enrollment updated successfully',
            'data'    => $enrollment->load(['student', 'section'])
        ]);
    }

    // DELETE /api/enrollments/{id}
    public function destroy($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        return response()->json([
            'message' => 'Enrollment deleted successfully'
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
}