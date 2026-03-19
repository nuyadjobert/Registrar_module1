<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::all();

        return response()->json([
            'message' => 'Subjects retrieved successfully',
            'data'    => $subjects
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_code' => 'required|string|unique:subjects,subject_code',
            'subject_name' => 'required|string',
            'units'        => 'required|integer|min:1',
            'type'         => 'required|in:Lecture,Laboratory,Lecture & Lab',
            'status'       => 'required|in:Active,Inactive',
        ]);

        $subject = Subject::create($validated);

        return response()->json([
            'message' => 'Subject created successfully',
            'data'    => $subject
        ], 201);
    }

    public function show($id)
    {
        $subject = Subject::findOrFail($id);

        return response()->json([
            'message' => 'Subject retrieved successfully',
            'data'    => $subject
        ]);
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'subject_code' => 'sometimes|string|unique:subjects,subject_code,' . $id,
            'subject_name' => 'sometimes|string',
            'units'        => 'sometimes|integer|min:1',
            'type'         => 'sometimes|in:Lecture,Laboratory,Lecture & Lab',
            'status'       => 'sometimes|in:Active,Inactive',
        ]);

        $subject->update($validated);

        return response()->json([
            'message' => 'Subject updated successfully',
            'data'    => $subject
        ]);
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return response()->json([
            'message' => 'Subject deleted successfully'
        ]);
    }
}