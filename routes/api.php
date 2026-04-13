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

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', [AuthController::class, 'user']);
});

/*
|--------------------------------------------------------------------------
| PROGRAMS (PUBLIC CRUD)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->prefix('programs')->group(function () {
    Route::get('/', [ProgramController::class, 'index']);
    Route::post('/', [ProgramController::class, 'store']);
    Route::get('/{id}', [ProgramController::class, 'show']);
    Route::put('/{id}', [ProgramController::class, 'update']);
    Route::delete('/{id}', [ProgramController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| SUBJECTS (PUBLIC CRUD)
|--------------------------------------------------------------------------
*/
Route::prefix('subjects')->group(function () {
    Route::get('/', [SubjectController::class, 'index']);
    Route::post('/', [SubjectController::class, 'store']);
    Route::get('/{id}', [SubjectController::class, 'show']);
    Route::put('/{id}', [SubjectController::class, 'update']);
    Route::delete('/{id}', [SubjectController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| CURRICULA (PUBLIC CRUD)
|--------------------------------------------------------------------------
*/
Route::prefix('curricula')->group(function () {
    Route::get('/', [CurriculumController::class, 'index']);
    Route::post('/', [CurriculumController::class, 'store']);
    Route::get('/{id}', [CurriculumController::class, 'show']);
    Route::put('/{id}', [CurriculumController::class, 'update']);
    Route::delete('/{id}', [CurriculumController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| SECTIONS (PUBLIC CRUD)
|--------------------------------------------------------------------------
*/
Route::prefix('sections')->group(function () {
    Route::get('/', [SectionController::class, 'index']);
    Route::post('/', [SectionController::class, 'store']);
    Route::get('/{id}', [SectionController::class, 'show']);
    Route::put('/{id}', [SectionController::class, 'update']);
    Route::delete('/{id}', [SectionController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| ENROLLMENTS (PUBLIC CRUD)
|--------------------------------------------------------------------------
*/
Route::prefix('enrollments')->group(function () {
    Route::get('/', [EnrollmentController::class, 'index']);
    Route::get('/{id}', [EnrollmentController::class, 'show']);
    Route::post('/{id}/approve', [EnrollmentController::class, 'approve']);
    Route::post('/{id}/reject', [EnrollmentController::class, 'reject']);
    Route::post('/{id}/mark-paid', [EnrollmentController::class, 'markAsPaid']);
});

/*
|--------------------------------------------------------------------------
| STUDENTS (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::prefix('students')->group(function () {
    Route::get('/', [StudentController::class, 'index']);
    Route::get('/{id}', [StudentController::class, 'show']);
    Route::get('/{id}/cor', [StudentController::class, 'cor']);
    Route::get('/{id}/transcript', [StudentController::class, 'transcript']);
    Route::get('/{id}/grades', [GradeController::class, 'byStudent']);
});

/*
|--------------------------------------------------------------------------
| GRADES (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::prefix('grades')->group(function () {
    Route::get('/', [GradeController::class, 'index']);
    Route::post('/', [GradeController::class, 'store']);
    Route::get('/{id}', [GradeController::class, 'show']);
    Route::put('/{id}', [GradeController::class, 'update']);
    Route::get('/sections/{id}/students', [GradeController::class, 'studentsBySection']);
});

/*
|--------------------------------------------------------------------------
| DOCUMENT REQUESTS (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::prefix('document-requests')->group(function () {
    Route::get('/', [DocumentRequestController::class, 'index']);
    Route::post('/', [DocumentRequestController::class, 'store']);
    Route::get('/{id}', [DocumentRequestController::class, 'show']);
    Route::post('/{id}/approve', [DocumentRequestController::class, 'approve']);
    Route::post('/{id}/reject', [DocumentRequestController::class, 'reject']);
});

/*
|--------------------------------------------------------------------------
| INSTRUCTORS (PUBLIC CRUD)
|--------------------------------------------------------------------------
*/
Route::prefix('instructors')->group(function () {
    Route::get('/', [InstructorController::class, 'index']);
    Route::post('/', [InstructorController::class, 'store']);
    Route::get('/{id}', [InstructorController::class, 'show']);
    Route::put('/{id}', [InstructorController::class, 'update']);
    Route::delete('/{id}', [InstructorController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| TERMS (PUBLIC CRUD)
|--------------------------------------------------------------------------
*/
Route::prefix('terms')->group(function () {
    Route::get('/', [TermController::class, 'index']);
    Route::post('/', [TermController::class, 'store']);
    Route::get('/{id}', [TermController::class, 'show']);
    Route::put('/{id}', [TermController::class, 'update']);
    Route::delete('/{id}', [TermController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| TEST ROUTE
|--------------------------------------------------------------------------
*/
Route::get('/test', function () {
    return response()->json(['status' => 'ok']);
});

Route::get('/create-admin', function () {
    $user = \App\Models\User::create([
        'name'     => 'Admin',
        'email'    => 'admin@admin.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
    ]);
    return response()->json($user);
});

//hayst
//help