<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Enrollment;
use App\Models\Grade;
use Illuminate\Validation\Rule;

class GradeController extends Controller
{
    /**
     * List grades (optionally by section)
     */
    public function index(Request $request)
    {
        $query = Grade::with('student', 'section.subject');

        if ($request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        $grades = $query->get();
        return response()->json($grades);
    }

    /**
     * Assign grade to student
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'section_id' => 'required|exists:sections,id',
            'grade' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $grade = Grade::updateOrCreate(
            ['student_id' => $request->student_id, 'section_id' => $request->section_id],
            ['grade' => $request->grade, 'remarks' => $request->remarks]
        );

        return response()->json($grade, 201);
    }

    /**
     * Update a grade
     */
    public function update(Request $request, $id)
    {
        $grade = Grade::findOrFail($id);

        $request->validate([
            'grade' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $grade->update($request->all());

        return response()->json($grade);
    }

    /**
     * Show a grade
     */
    public function show($id)
    {
        $grade = Grade::with('student', 'section.subject')->findOrFail($id);
        return response()->json($grade);
    }
}