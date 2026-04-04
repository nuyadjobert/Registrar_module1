<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index()
    {
        return response()->json(Subject::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_code' => 'required|string|unique:subjects,subject_code',
            'subject_name' => 'required|string',
            'units' => 'required|integer|min:0',
            'type' => 'nullable|string',
            'status' => ['nullable', Rule::in(['active','inactive'])],
        ]);

        $subject = Subject::create($request->all());

        return response()->json($subject, 201);
    }

    public function show($id)
    {
        $subject = Subject::with('sections')->findOrFail($id);
        return response()->json($subject);
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate([
            'subject_code' => [
                'required',
                'string',
                Rule::unique('subjects')->ignore($subject->id),
            ],
            'subject_name' => 'required|string',
            'units' => 'required|integer|min:0',
            'type' => 'nullable|string',
            'status' => ['nullable', Rule::in(['active','inactive'])],
        ]);

        $subject->update($request->all());

        return response()->json($subject);
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return response()->json(['message' => 'Subject deleted successfully']);
    }
}