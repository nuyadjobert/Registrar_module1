<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use Illuminate\Http\Request;

class DocumentRequestController extends Controller
{
    // GET /api/document-requests
    public function index()
    {
        $requests = DocumentRequest::with('student')->get();

        return response()->json([
            'message' => 'Document requests retrieved successfully',
            'data'    => $requests
        ]);
    }

    // POST /api/document-requests
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'type'       => 'required|in:cor,tor',
        ]);

        $existing = DocumentRequest::where('student_id', $validated['student_id'])
                                   ->where('type', $validated['type'])
                                   ->where('status', 'pending')
                                   ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Student already has a pending ' . strtoupper($validated['type']) . ' request'
            ], 422);
        }

        $docRequest = DocumentRequest::create([
            'student_id' => $validated['student_id'],
            'type'       => $validated['type'],
            'status'     => 'pending',
        ]);

        return response()->json([
            'message' => strtoupper($validated['type']) . ' request submitted successfully',
            'data'    => $docRequest->load('student')
        ], 201);
    }

    // POST /api/document-requests/{id}/release
    public function release($id)
    {
        $docRequest = DocumentRequest::findOrFail($id);

        if ($docRequest->status === 'released') {
            return response()->json([
                'message' => 'Document is already released'
            ], 422);
        }

        if ($docRequest->payment_status !== 'paid') {
            return response()->json([
                'message' => 'Cannot release document. Student has not yet paid.'
            ], 422);
        }

        $docRequest->update(['status' => 'released']);

        return response()->json([
            'message' => strtoupper($docRequest->type) . ' released successfully',
            'data'    => $docRequest->load('student')
        ]);
    }

    // POST /api/document-requests/{id}/unrelease
    public function unrelease($id)
    {
        $docRequest = DocumentRequest::findOrFail($id);

        if ($docRequest->status !== 'released') {
            return response()->json([
                'message' => 'Document is not yet released'
            ], 422);
        }

        $docRequest->update(['status' => 'unreleased']);

        return response()->json([
            'message' => strtoupper($docRequest->type) . ' unreleased successfully',
            'data'    => $docRequest->load('student')
        ]);
    }

    // POST /api/cashier/document-requests/{id}/mark-paid  ← cashier calls this
    public function markAsPaid($id)
    {
        $docRequest = DocumentRequest::findOrFail($id);

        if ($docRequest->payment_status === 'paid') {
            return response()->json([
                'message' => 'Document request is already marked as paid'
            ], 422);
        }

        $docRequest->update(['payment_status' => 'paid']);

        return response()->json([
            'message' => 'Payment status updated successfully',
            'data'    => $docRequest->load('student')
        ]);
    }
}