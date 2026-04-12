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
$sections = Section::with([
    'subject.programs', // ✅ IMPORTANT
    'instructor'
])->get();
        return response()->json($sections);
    }

    public function store(Request $request)
{
    // 1. Validate exactly what you expect
    $validated = $request->validate([
        'section_name'          => 'required|string', // Ensure frontend sends "name"
        'subject_id'    => 'required|exists:subjects,id',
        'instructor_id' => 'required|exists:instructors,id',
        'term_id'       => 'required|exists:terms,id',
        'capacity'      => 'nullable|integer|min:1',
        'schedule'      => 'nullable|string',
        'room'          => 'nullable|string',
        'status'        => ['nullable', Rule::in(['open', 'closed'])],
    ]);

    // 2. Use $validated instead of $request->all()
    $section = Section::create($validated);

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