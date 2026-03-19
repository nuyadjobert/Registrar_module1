<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index()
    {
        $curricula = Curriculum::with(['course', 'subject'])->get();

        return response()->json([
            'message' => 'Curricula retrieved successfully',
            'data'    => $curricula
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id'   => 'required|exists:courses,id',
            'subject_id'  => 'required|exists:subjects,id',
            'year_level'  => 'required|integer|between:1,4',
            'semester'    => 'required|integer|between:1,2',
            'school_year' => 'required|string',
            'status'      => 'required|in:Active,Inactive',
        ]);

        $curriculum = Curriculum::create($validated);

        return response()->json([
            'message' => 'Curriculum created successfully',
            'data'    => $curriculum->load(['course', 'subject'])
        ], 201);
    }

    public function show($id)
    {
        $curriculum = Curriculum::with(['course', 'subject'])->findOrFail($id);

        return response()->json([
            'message' => 'Curriculum retrieved successfully',
            'data'    => $curriculum
        ]);
    }

    public function update(Request $request, $id)
    {
        $curriculum = Curriculum::findOrFail($id);

        $validated = $request->validate([
            'course_id'   => 'sometimes|exists:courses,id',
            'subject_id'  => 'sometimes|exists:subjects,id',
            'year_level'  => 'sometimes|integer|between:1,4',
            'semester'    => 'sometimes|integer|between:1,2',
            'school_year' => 'sometimes|string',
            'status'      => 'sometimes|in:Active,Inactive',
        ]);

        $curriculum->update($validated);

        return response()->json([
            'message' => 'Curriculum updated successfully',
            'data'    => $curriculum->load(['course', 'subject'])
        ]);
    }

    public function destroy($id)
    {
        $curriculum = Curriculum::findOrFail($id);
        $curriculum->delete();

        return response()->json([
            'message' => 'Curriculum deleted successfully'
        ]);
    }
}