<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    /**
     * List all programs
     */
    public function index()
    {
        $programs = Program::all();
        return response()->json($programs);
    }

    /**
     * Store a new program
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:programs,code',
            'name' => 'required|string',
            'department' => 'nullable|string',
            'status' => ['nullable', Rule::in(['active','inactive'])],
        ]);

        $program = Program::create($request->all());

        return response()->json($program, 201);
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

        $request->validate([
            'code' => [
                'required',
                'string',
                Rule::unique('programs')->ignore($program->id),
            ],
            'name' => 'required|string',
            'department' => 'nullable|string',
            'status' => ['nullable', Rule::in(['active','inactive'])],
        ]);

        $program->update($request->all());

        return response()->json($program);
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