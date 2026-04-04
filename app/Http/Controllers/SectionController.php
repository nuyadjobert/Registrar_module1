<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::with('subject', 'instructor')->get();
        return response()->json($sections);
    }

    public function store(Request $request)
    {
        $request->validate([
            'section_name' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'instructor_id' => 'required|exists:instructors,id',
            'term_id' => 'required|exists:terms,id',
            'capacity' => 'nullable|integer|min:1',
            'schedule' => 'nullable|string',
            'room' => 'nullable|string',
            'status' => ['nullable', Rule::in(['open','closed'])],
        ]);

        $section = Section::create($request->all());

        return response()->json($section, 201);
    }

    public function show($id)
    {
        $section = Section::with('subject','instructor','enrollments')->findOrFail($id);
        return response()->json($section);
    }

    public function update(Request $request, $id)
    {
        $section = Section::findOrFail($id);

        $request->validate([
            'section_name' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'instructor_id' => 'required|exists:instructors,id',
            'term_id' => 'required|exists:terms,id',
            'capacity' => 'nullable|integer|min:1',
            'schedule' => 'nullable|string',
            'room' => 'nullable|string',
            'status' => ['nullable', Rule::in(['open','closed'])],
        ]);

        $section->update($request->all());

        return response()->json($section);
    }

    public function destroy($id)
    {
        $section = Section::findOrFail($id);
        $section->delete();

        return response()->json(['message' => 'Section deleted successfully']);
    }
}