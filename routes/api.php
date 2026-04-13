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
use App\Http\Controllers\ExternalStudentController;

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
| PROGRAMS
|--------------------------------------------------------------------------
*/
Route::prefix('programs')->group(function () {
    // Public GET routes
    Route::get('/', [ProgramController::class, 'index']);
    Route::get('/{id}', [ProgramController::class, 'show']);
    
    // Protected POST/PUT/DELETE routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [ProgramController::class, 'store']);
        Route::put('/{id}', [ProgramController::class, 'update']);
        Route::delete('/{id}', [ProgramController::class, 'destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| SUBJECTS
|--------------------------------------------------------------------------
*/
Route::prefix('subjects')->group(function () {
    // Public GET routes
    Route::get('/', [SubjectController::class, 'index']);
    Route::get('/{id}', [SubjectController::class, 'show']);
    
    // Protected POST/PUT/DELETE routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [SubjectController::class, 'store']);
        Route::put('/{id}', [SubjectController::class, 'update']);
        Route::delete('/{id}', [SubjectController::class, 'destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| CURRICULA
|--------------------------------------------------------------------------
*/
Route::prefix('curricula')->group(function () {
    // Public GET routes
    Route::get('/', [CurriculumController::class, 'index']);
    Route::get('/{id}', [CurriculumController::class, 'show']);
    
    // Protected POST/PUT/DELETE routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [CurriculumController::class, 'store']);
        Route::put('/{id}', [CurriculumController::class, 'update']);
        Route::delete('/{id}', [CurriculumController::class, 'destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| SECTIONS
|--------------------------------------------------------------------------
*/
Route::prefix('sections')->group(function () {
    // Public GET routes
    Route::get('/', [SectionController::class, 'index']);
    Route::get('/{id}', [SectionController::class, 'show']);
    
    // Protected POST/PUT/DELETE routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [SectionController::class, 'store']);
        Route::put('/{id}', [SectionController::class, 'update']);
        Route::delete('/{id}', [SectionController::class, 'destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| ENROLLMENTS
|--------------------------------------------------------------------------
*/
Route::prefix('enrollments')->group(function () {
    // Public GET routes
    Route::get('/', [EnrollmentController::class, 'index']);
    Route::get('/{id}', [EnrollmentController::class, 'show']);
    
    // Protected POST routes (approve, reject, mark as paid)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/{id}/approve', [EnrollmentController::class, 'approve']);
        Route::post('/{id}/reject', [EnrollmentController::class, 'reject']);
        Route::post('/{id}/mark-paid', [EnrollmentController::class, 'markAsPaid']);
    });
});

/*
|--------------------------------------------------------------------------
| STUDENTS
|--------------------------------------------------------------------------
*/
Route::prefix('students')->group(function () {
    // All GET routes are public
    Route::get('/', [StudentController::class, 'index']);
    Route::get('/{id}', [StudentController::class, 'show']);
    Route::get('/{id}/cor', [StudentController::class, 'cor']);
    Route::get('/{id}/transcript', [StudentController::class, 'transcript']);
    Route::get('/{id}/grades', [GradeController::class, 'byStudent']);
});

/*
|--------------------------------------------------------------------------
| GRADES
|--------------------------------------------------------------------------
*/
Route::prefix('grades')->group(function () {
    // Public GET routes
    Route::get('/', [GradeController::class, 'index']);
    Route::get('/{id}', [GradeController::class, 'show']);
    Route::get('/sections/{id}/students', [GradeController::class, 'studentsBySection']);
    
    // Protected POST/PUT routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [GradeController::class, 'store']);
        Route::put('/{id}', [GradeController::class, 'update']);
    });
});

/*
|--------------------------------------------------------------------------
| DOCUMENT REQUESTS
|--------------------------------------------------------------------------
*/
Route::prefix('document-requests')->group(function () {
    // Public GET routes
    Route::get('/', [DocumentRequestController::class, 'index']);
    Route::get('/{id}', [DocumentRequestController::class, 'show']);
    
    // Protected POST routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [DocumentRequestController::class, 'store']);
        Route::post('/{id}/approve', [DocumentRequestController::class, 'approve']);
        Route::post('/{id}/reject', [DocumentRequestController::class, 'reject']);
    });
});

/*
|--------------------------------------------------------------------------
| INSTRUCTORS
|--------------------------------------------------------------------------
*/
Route::prefix('instructors')->group(function () {
    // Public GET routes
    Route::get('/', [InstructorController::class, 'index']);
    Route::get('/{id}', [InstructorController::class, 'show']);
    
    // Protected POST/PUT/DELETE routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [InstructorController::class, 'store']);
        Route::put('/{id}', [InstructorController::class, 'update']);
        Route::delete('/{id}', [InstructorController::class, 'destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| TERMS
|--------------------------------------------------------------------------
*/
Route::prefix('terms')->group(function () {
    // Public GET routes
    Route::get('/', [TermController::class, 'index']);
    Route::get('/{id}', [TermController::class, 'show']);
    
    // Protected POST/PUT/DELETE routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [TermController::class, 'store']);
        Route::put('/{id}', [TermController::class, 'update']);
        Route::delete('/{id}', [TermController::class, 'destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| EXTERNAL SYNC (INTEGRATION ROUTES)
|--------------------------------------------------------------------------
*/


Route::prefix('external')->group(function () {

    Route::get('/students/sync', [ExternalStudentController::class, 'syncStudents']);

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