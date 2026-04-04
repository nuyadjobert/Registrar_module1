<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curriculum;
use Illuminate\Validation\Rule;

class CurriculumController extends Controller
{
    public function index()
    {
        $curricula = Curriculum::with('program', 'subject')->get();
        return response()->json($curricula);
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'subject_id' => 'required|exists:subjects,id',
            'year_level' => 'required|integer|min:1|max:5',
            'semester' => 'required|string',
            'school_year' => 'required|string',
            'status' => ['nullable', Rule::in(['active','inactive'])],
        ]);

        $curriculum = Curriculum::create($request->all());

        return response()->json($curriculum, 201);
    }

    public function show($id)
    {
        $curriculum = Curriculum::with('program','subject')->findOrFail($id);
        return response()->json($curriculum);
    }

    public function update(Request $request, $id)
    {
        $curriculum = Curriculum::findOrFail($id);

        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'subject_id' => 'required|exists:subjects,id',
            'year_level' => 'required|integer|min:1|max:5',
            'semester' => 'required|string',
            'school_year' => 'required|string',
            'status' => ['nullable', Rule::in(['active','inactive'])],
        ]);

        $curriculum->update($request->all());

        return response()->json($curriculum);
    }

    public function destroy($id)
    {
        $curriculum = Curriculum::findOrFail($id);
        $curriculum->delete();

        return response()->json(['message' => 'Curriculum entry deleted successfully']);
    }
}