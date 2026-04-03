<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\DocumentRequestController;

// Cashier Module Routes
// api/cashier

Route::prefix('cashier')->name('cashier.')->group(function () {

    // POST /api/cashier/enrollments/{id}/mark-paid
    Route::post('/enrollments/{id}/mark-paid', [EnrollmentController::class, 'markAsPaid'])
        ->name('enrollments.mark-paid');

    // add more cashier routes here later...

Route::post('/document-requests/{id}/mark-paid', [DocumentRequestController::class, 'markAsPaid'])
    ->name('document-requests.mark-paid');
});