<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\TermController;

// ==============================
// AUTH ROUTES
// ==============================
Route::prefix('auth')->name('auth.')->group(function () {

    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/user', [AuthController::class, 'user'])->name('user');
    });

});

// ==============================
// PROTECTED ROUTES (Registrar)
// ==============================

Route::get('terms', [TermController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {

    // -------- PROGRAMS --------
    Route::prefix('programs')->name('programs.')->group(function () {
        Route::get('/', [ProgramController::class, 'index'])->name('index');
        Route::post('/', [ProgramController::class, 'store'])->name('store');
        Route::get('/{id}', [ProgramController::class, 'show'])->name('show');
        Route::put('/{id}', [ProgramController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProgramController::class, 'destroy'])->name('destroy');
    });

    // -------- SUBJECTS --------
    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        Route::post('/', [SubjectController::class, 'store'])->name('store');
        Route::get('/{id}', [SubjectController::class, 'show'])->name('show');
        Route::put('/{id}', [SubjectController::class, 'update'])->name('update');
        Route::delete('/{id}', [SubjectController::class, 'destroy'])->name('destroy');
    });

    // -------- CURRICULA --------
    Route::prefix('curricula')->name('curricula.')->group(function () {
        Route::get('/', [CurriculumController::class, 'index'])->name('index');
        Route::post('/', [CurriculumController::class, 'store'])->name('store');
        Route::get('/{id}', [CurriculumController::class, 'show'])->name('show');
        Route::put('/{id}', [CurriculumController::class, 'update'])->name('update');
        Route::delete('/{id}', [CurriculumController::class, 'destroy'])->name('destroy');
    });

    // -------- SECTIONS --------
    Route::prefix('sections')->name('sections.')->group(function () {
        Route::get('/', [SectionController::class, 'index'])->name('index');
        Route::post('/', [SectionController::class, 'store'])->name('store');
        Route::get('/{id}', [SectionController::class, 'show'])->name('show');
        Route::put('/{id}', [SectionController::class, 'update'])->name('update');
        Route::delete('/{id}', [SectionController::class, 'destroy'])->name('destroy');
    });

    // -------- ENROLLMENTS --------
    Route::prefix('enrollments')->name('enrollments.')->group(function () {
        Route::get('/', [EnrollmentController::class, 'index'])->name('index');
        Route::get('/{id}', [EnrollmentController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [EnrollmentController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [EnrollmentController::class, 'reject'])->name('reject');
        Route::post('/{id}/mark-paid', [EnrollmentController::class, 'markAsPaid'])->name('mark-paid');
    });

    // -------- STUDENTS --------
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/{id}', [StudentController::class, 'show'])->name('show');
        Route::get('/{id}/cor', [StudentController::class, 'cor'])->name('cor');
        Route::get('/{id}/transcript', [StudentController::class, 'transcript'])->name('transcript');
    });

    // -------- GRADES --------
    Route::prefix('grades')->name('grades.')->group(function () {
        Route::get('/', [GradeController::class, 'index'])->name('index');
        Route::post('/', [GradeController::class, 'store'])->name('store');
        Route::get('/{id}', [GradeController::class, 'show'])->name('show');
        Route::put('/{id}', [GradeController::class, 'update'])->name('update');
    });

    // -------- DOCUMENT REQUESTS --------
    Route::prefix('document-requests')->name('document-requests.')->group(function () {
        Route::get('/', [DocumentRequestController::class, 'index'])->name('index');
        Route::post('/', [DocumentRequestController::class, 'store'])->name('store');
        Route::get('/{id}', [DocumentRequestController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [DocumentRequestController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [DocumentRequestController::class, 'reject'])->name('reject');
    });

    // -------- INSTRUCTORS --------
Route::prefix('instructors')->name('instructors.')->group(function () {
    Route::get('/', [InstructorController::class, 'index'])->name('index');
    Route::post('/', [InstructorController::class, 'store'])->name('store');
    Route::get('/{id}', [InstructorController::class, 'show'])->name('show');
    Route::put('/{id}', [InstructorController::class, 'update'])->name('update');
    Route::delete('/{id}', [InstructorController::class, 'destroy'])->name('destroy');
});

// -------- TERMS --------
Route::prefix('terms')->name('terms.')->group(function () {
    // Route::get('/', [TermController::class, 'index'])->name('index');
    Route::post('/', [TermController::class, 'store'])->name('store');
    Route::get('/{id}', [TermController::class, 'show'])->name('show');
    Route::put('/{id}', [TermController::class, 'update'])->name('update');
    Route::delete('/{id}', [TermController::class, 'destroy'])->name('destroy');
});

});

// -------- TEST ROUTE --------
Route::get('/test', function () {
    return response()->json(['status' => 'ok']);
})->name('test');