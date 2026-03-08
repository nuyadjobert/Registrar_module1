<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\StudentController;

// =====================
// Auth
// =====================
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// =====================
// Courses
// =====================
Route::post('/courses',        [CourseController::class, 'store']);
Route::get('/courses',         [CourseController::class, 'index']);
Route::get('/courses/{id}',    [CourseController::class, 'show']);
Route::put('/courses/{id}',    [CourseController::class, 'update']);
Route::delete('/courses/{id}', [CourseController::class, 'destroy']);

// =====================
// Curriculum
// =====================
Route::post('/curriculum',        [CurriculumController::class, 'store']);
Route::get('/curriculum',         [CurriculumController::class, 'index']);
Route::get('/curriculum/{id}',    [CurriculumController::class, 'show']);
Route::put('/curriculum/{id}',    [CurriculumController::class, 'update']);
Route::delete('/curriculum/{id}', [CurriculumController::class, 'destroy']);

// =====================
// Sections
// =====================
Route::post('/sections',        [SectionController::class, 'store']);
Route::get('/sections',         [SectionController::class, 'index']);
Route::get('/sections/{id}',    [SectionController::class, 'show']);
Route::put('/sections/{id}',    [SectionController::class, 'update']);
Route::delete('/sections/{id}', [SectionController::class, 'destroy']);

// =====================
// Protected Routes (Login Required)
// =====================
Route::middleware('auth:sanctum')->group(function () {

    // Enrollments
    Route::get('/enrollments',               [EnrollmentController::class, 'index']);
    Route::get('/enrollments/{id}',          [EnrollmentController::class, 'show']);
    Route::post('/enrollments/{id}/approve', [EnrollmentController::class, 'approve']);
    Route::post('/enrollments/{id}/reject',  [EnrollmentController::class, 'reject']);

    // Students
    Route::get('/students',                  [StudentController::class, 'index']);
    Route::get('/students/{id}',             [StudentController::class, 'show']);
    Route::get('/students/{id}/cor',         [StudentController::class, 'cor']);
    Route::get('/students/{id}/transcript',  [StudentController::class, 'transcript']);
});

// =====================
// Test
// =====================
Route::get('/test', function () {
    return response()->json(['status' => 'ok']);
});