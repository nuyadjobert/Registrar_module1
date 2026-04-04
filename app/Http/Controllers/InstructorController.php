<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Instructor;
use Illuminate\Validation\Rule;

class InstructorController extends Controller
{
    public function index()
    {
        return response()->json(Instructor::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'nullable|email|unique:instructors,email',
            'department' => 'nullable|string',
            'status' => ['nullable', Rule::in(['active','inactive'])],
        ]);

        $instructor = Instructor::create($request->all());

        return response()->json($instructor, 201);
    }

    public function show($id)
    {
        $instructor = Instructor::with('sections')->findOrFail($id);
        return response()->json($instructor);
    }

    public function update(Request $request, $id)
    {
        $instructor = Instructor::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'email' => [
                'nullable',
                'email',
                Rule::unique('instructors')->ignore($instructor->id),
            ],
            'department' => 'nullable|string',
            'status' => ['nullable', Rule::in(['active','inactive'])],
        ]);

        $instructor->update($request->all());

        return response()->json($instructor);
    }

    public function destroy($id)
    {
        $instructor = Instructor::findOrFail($id);
        $instructor->delete();

        return response()->json(['message' => 'Instructor deleted successfully']);
    }
}