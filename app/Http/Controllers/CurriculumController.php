<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    // GET /api/curriculum
    public function index()
    {
        $curricula = Curriculum::with(['course', 'subject'])->get();

        return response()->json([
            'message' => 'Curricula retrieved successfully',
            'data'    => $curricula
        ]);
    }

    // POST /api/curriculum
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id'  => 'required|exists:courses,id',
            'subject_id' => 'required|exists:subjects,id',
            'year_level' => 'required|integer|between:1,4',
            'semester'   => 'required|integer|between:1,2',
        ]);

        $curriculum = Curriculum::create($validated);

        return response()->json([
            'message' => 'Curriculum created successfully',
            'data'    => $curriculum->load(['course', 'subject'])
        ], 201);
    }

    // GET /api/curriculum/{id}
    public function show($id)
    {
        $curriculum = Curriculum::with(['course', 'subject'])->findOrFail($id);

        return response()->json([
            'message' => 'Curriculum retrieved successfully',
            'data'    => $curriculum
        ]);
    }

    // PUT /api/curriculum/{id}
    public function update(Request $request, $id)
    {
        $curriculum = Curriculum::findOrFail($id);

        $validated = $request->validate([
            'course_id'  => 'sometimes|exists:courses,id',
            'subject_id' => 'sometimes|exists:subjects,id',
            'year_level' => 'sometimes|integer|between:1,4',
            'semester'   => 'sometimes|integer|between:1,2',
        ]);

        $curriculum->update($validated);

        return response()->json([
            'message' => 'Curriculum updated successfully',
            'data'    => $curriculum->load(['course', 'subject'])
        ]);
    }

    // DELETE /api/curriculum/{id}
    public function destroy($id)
    {
        $curriculum = Curriculum::findOrFail($id);
        $curriculum->delete();

        return response()->json([
            'message' => 'Curriculum deleted successfully'
        ]);
    }
}