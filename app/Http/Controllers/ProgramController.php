<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    /**
     * List all programs
     */
    public function index(Request $request)
    {
        $query = Program::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $programs = $query->get();
        return response()->json($programs);
    }

    /**
     * Store a new program
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'       => [
                'required',
                'string',
                'max:10',
                Rule::unique('programs', 'code'),
            ],
            'name'       => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'status'     => ['nullable', Rule::in(['active', 'inactive'])],
        ], [
            'code.unique' => 'A program with this code already exists.',
            'name.unique' => 'A program with this name already exists.', // we'll add composite below
        ]);

        // Additional composite unique check: name + department
        $exists = Program::where('name', $validated['name'])
                         ->where('department', $validated['department'])
                         ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'A program with this name and department already exists.',
                'errors'  => ['department' => 'A program with this name and department already exists.']
            ], 422);
        }

        $program = Program::create($validated);

        return response()->json([
            'message' => 'Program created successfully',
            'program' => $program
        ], 201);
    }

    /**
     * Show a single program
     */
    public function show($id)
    {
        $program = Program::with('students', 'curricula')->findOrFail($id);
        return response()->json($program);
    }

    /**
     * Update a program
     */
    public function update(Request $request, $id)
    {
        $program = Program::findOrFail($id);

        $validated = $request->validate([
            'code'       => [
                'required',
                'string',
                'max:10',
                Rule::unique('programs', 'code')->ignore($program->id),
            ],
            'name'       => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'status'     => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        // Composite unique check for name + department (ignore current record)
        $exists = Program::where('name', $validated['name'])
                         ->where('department', $validated['department'])
                         ->where('id', '!=', $program->id)
                         ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'A program with this name and department already exists.',
                'errors'  => ['department' => 'A program with this name and department already exists.']
            ], 422);
        }

        $program->update($validated);

        return response()->json([
            'message' => 'Program updated successfully',
            'program' => $program
        ]);
    }

    /**
     * Delete a program
     */
    public function destroy($id)
    {
        $program = Program::findOrFail($id);
        $program->delete();

        return response()->json(['message' => 'Program deleted successfully']);
    }
}