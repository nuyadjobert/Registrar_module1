<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentRequest;
use Illuminate\Validation\Rule;

class DocumentRequestController extends Controller
{
    /**
     * List all document requests
     */
    public function index()
    {
        $requests = DocumentRequest::with('student')->get();
        return response()->json($requests);
    }

    /**
     * Store a new document request (COR / TOR)
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => ['required', Rule::in(['COR','TOR'])],
            'payment_status' => ['nullable', Rule::in(['paid','unpaid'])],
            'status' => ['nullable', Rule::in(['pending','approved','rejected'])],
        ]);

        $document = DocumentRequest::create($request->all());

        return response()->json($document, 201);
    }

    /**
     * Approve a request
     */
    public function approve($id)
    {
        $document = DocumentRequest::findOrFail($id);
        $document->update(['status' => 'approved']);
        return response()->json(['message' => 'Document request approved', 'document' => $document]);
    }

    /**
     * Reject a request
     */
    public function reject($id)
    {
        $document = DocumentRequest::findOrFail($id);
        $document->update(['status' => 'rejected']);
        return response()->json(['message' => 'Document request rejected', 'document' => $document]);
    }

    /**
     * Show a single document request
     */
    public function show($id)
    {
        $document = DocumentRequest::with('student')->findOrFail($id);
        return response()->json($document);
    }
}