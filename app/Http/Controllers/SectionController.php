<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    // GET /api/sections
    public function index()
    {
        $sections = Section::with('subject')->get();

        return response()->json([
            'message' => 'Sections retrieved successfully',
            'data'    => $sections
        ]);
    }

    // POST /api/sections
    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_name' => 'required|string|unique:sections',
            'subject_id'   => 'required|exists:subjects,id',
            'capacity'     => 'required|integer|min:1',
            'school_year'  => 'required|string',
            'semester'     => 'required|in:1st Semester,2nd Semester,Summer',
            'status'       => 'required|in:Open,Closed,Full',
        ]);

        $section = Section::create($validated);

        return response()->json([
            'message' => 'Section created successfully',
            'data'    => $section->load('subject')
        ], 201);
    }

    // GET /api/sections/{id}
    public function show($id)
    {
        $section = Section::with('subject')->findOrFail($id);

        return response()->json([
            'message' => 'Section retrieved successfully',
            'data'    => $section
        ]);
    }

    // PUT /api/sections/{id}
    public function update(Request $request, $id)
    {
        $section = Section::findOrFail($id);

        $validated = $request->validate([
            'section_name' => 'sometimes|string|unique:sections,section_name,' . $id,
            'subject_id'   => 'sometimes|exists:subjects,id',
            'capacity'     => 'sometimes|integer|min:1',
            'school_year'  => 'sometimes|string',
            'semester'     => 'sometimes|in:1st Semester,2nd Semester,Summer',
            'status'       => 'sometimes|in:Open,Closed,Full',
        ]);

        $section->update($validated);

        return response()->json([
            'message' => 'Section updated successfully',
            'data'    => $section->load('subject')
        ]);
    }

    // DELETE /api/sections/{id}
    public function destroy($id)
    {
        $section = Section::findOrFail($id);
        $section->delete();

        return response()->json([
            'message' => 'Section deleted successfully'
        ]);
    }
}