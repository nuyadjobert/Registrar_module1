<?php

namespace App\Http\Controllers;

use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index()
    {
        return response()->json(Term::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'is_active' => 'boolean'
        ]);

        $term = Term::create($validated);

        return response()->json($term, 201);
    }

    public function show($id)
    {
        return response()->json(Term::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $term = Term::findOrFail($id);

        $term->update($request->all());

        return response()->json($term);
    }

    public function destroy($id)
    {
        Term::destroy($id);

        return response()->json(['message' => 'Deleted successfully']);
    }
}