<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with('programs');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $subjects = $query->get();
        return response()->json($subjects);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_code' => 'required|string|unique:subjects,subject_code',
            'subject_name' => 'required|string',
            'units'        => 'required|integer|min:0',
            'type'         => 'nullable|string',
            'status'       => ['nullable', Rule::in(['active', 'inactive'])],
            'program_id'   => 'required|exists:programs,id',
            'year_level'   => 'nullable|integer',
            'semester'     => 'nullable|string',
            'school_year' => [
                'nullable',
                'regex:/^\d{4}-\d{4}$/',
            ],
        ]);

        $subject = Subject::create([
            'subject_code' => $request->subject_code,
            'subject_name' => $request->subject_name,
            'units'        => $request->units,
            'type'         => $request->type,
            'status'       => $request->status ?? 'active',
        ]);

        // Attach to program via curricula pivot table
        $subject->programs()->attach($request->program_id, [
            'year_level'  => $request->year_level,
            'semester'    => $request->semester,
            'school_year' => $request->school_year ?? date('Y'),
            'status'      => 'active',
        ]);

        return response()->json($subject->load('programs'), 201);
    }

    public function show($id)
    {
        $subject = Subject::with('sections', 'programs')->findOrFail($id);
        return response()->json($subject);
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate([
            'subject_code' => ['required', 'string', Rule::unique('subjects')->ignore($subject->id)],
            'subject_name' => 'required|string',
            'units'        => 'required|integer|min:0',
            'type'         => 'nullable|string',
            'status'       => ['nullable', Rule::in(['active', 'inactive'])],
            'program_id'   => 'nullable|exists:programs,id',
            'year_level'   => 'nullable|integer',
            'semester'     => 'nullable|string',
            'school_year' => [
                'nullable',
                'regex:/^\d{4}-\d{4}$/',
            ],
        ]);

        $subject->update([
            'subject_code' => $request->subject_code,
            'subject_name' => $request->subject_name,
            'units'        => $request->units,
            'type'         => $request->type,
            'status'       => $request->status,
        ]);

        // Update program association if provided
        if ($request->has('program_id')) {
            $subject->programs()->sync([$request->program_id => [
                'year_level'  => $request->year_level,
                'semester'    => $request->semester,
                'school_year' => $request->school_year ?? date('Y'),
                'status'      => 'active',
            ]]);
        }

        return response()->json($subject->load('programs'));
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return response()->json(['message' => 'Subject deleted successfully']);
    }
}
