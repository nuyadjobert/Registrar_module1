<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    // GET /api/subjects
    public function index()
    {
        $subjects = Subject::all();

        return response()->json([
            'message' => 'Subjects retrieved successfully',
            'data'    => $subjects
        ]);
    }

    // POST /api/subjects
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_code' => 'required|string|unique:subjects,subject_code',
            'subject_name' => 'required|string',
            'units'        => 'required|integer|min:1',
        ]);

        $subject = Subject::create($validated);

        return response()->json([
            'message' => 'Subject created successfully',
            'data'    => $subject
        ], 201);
    }

    // GET /api/subjects/{id}
    public function show($id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json([
                'message' => 'Subject not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Subject retrieved successfully',
            'data'    => $subject
        ]);
    }

    // PUT /api/subjects/{id}
    public function update(Request $request, $id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json([
                'message' => 'Subject not found'
            ], 404);
        }

        $validated = $request->validate([
            'subject_code' => 'sometimes|string|unique:subjects,subject_code,' . $id,
            'subject_name' => 'sometimes|string',
            'units'        => 'sometimes|integer|min:1',
        ]);

        $subject->update($validated);

        return response()->json([
            'message' => 'Subject updated successfully',
            'data'    => $subject
        ]);
    }

    // DELETE /api/subjects/{id}
    public function destroy($id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json([
                'message' => 'Subject not found'
            ], 404);
        }

        $subject->delete();

        return response()->json([
            'message' => 'Subject deleted successfully'
        ]);
    }
}