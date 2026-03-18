<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;


// auth routes
//api/auth
Route::prefix('auth')->name('auth.')->group(function () {

    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login',    [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/user',    [AuthController::class, 'user'])->name('user');

    });

});


//protected routes
Route::middleware('auth:sanctum')->group(function () {

 //courses routes
 //api/courses
    Route::prefix('courses')->name('courses.')->group(function () {

        Route::get('/',      [CourseController::class, 'index'])->name('index');
        Route::post('/',     [CourseController::class, 'store'])->name('store');
        Route::get('/{id}',  [CourseController::class, 'show'])->name('show');
        Route::put('/{id}',  [CourseController::class, 'update'])->name('update');
        Route::delete('/{id}', [CourseController::class, 'destroy'])->name('destroy');

    });

//curriculum routes
//api/curriculum
    Route::prefix('curriculum')->name('curriculum.')->group(function () {

        Route::get('/',      [CurriculumController::class, 'index'])->name('index');
        Route::post('/',     [CurriculumController::class, 'store'])->name('store');
        Route::get('/{id}',  [CurriculumController::class, 'show'])->name('show');
        Route::put('/{id}',  [CurriculumController::class, 'update'])->name('update');
        Route::delete('/{id}', [CurriculumController::class, 'destroy'])->name('destroy');

    });

//sections routes
//api/sections
    Route::prefix('sections')->name('sections.')->group(function () {

        Route::get('/',      [SectionController::class, 'index'])->name('index');
        Route::post('/',     [SectionController::class, 'store'])->name('store');
        Route::get('/{id}',  [SectionController::class, 'show'])->name('show');
        Route::put('/{id}',  [SectionController::class, 'update'])->name('update');
        Route::delete('/{id}', [SectionController::class, 'destroy'])->name('destroy');

    });


//enrollments routes
//api/enrollments
    Route::prefix('enrollments')->name('enrollments.')->group(function () {

        Route::get('/', [EnrollmentController::class, 'index'])->name('index');
        Route::get('/{id}', [EnrollmentController::class, 'show'])->name('show');

        Route::post('/{id}/approve', [EnrollmentController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject',  [EnrollmentController::class, 'reject'])->name('reject');

    });


    //students routes
    //api/students
    Route::prefix('students')->name('students.')->group(function () {

        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/{id}', [StudentController::class, 'show'])->name('show');

        Route::get('/{id}/cor',        [StudentController::class, 'cor'])->name('cor');
        Route::get('/{id}/transcript', [StudentController::class, 'transcript'])->name('transcript');

    });

    //subjects routes
//api/subjects
    Route::prefix('subjects')->name('subjects.')->group(function () {

        Route::get('/',        [SubjectController::class, 'index'])->name('index');
        Route::post('/',       [SubjectController::class, 'store'])->name('store');
        Route::get('/{id}',    [SubjectController::class, 'show'])->name('show');
        Route::put('/{id}',    [SubjectController::class, 'update'])->name('update');
        Route::delete('/{id}', [SubjectController::class, 'destroy'])->name('destroy');

    });

});


//test routes
Route::get('/test', function () {
    return response()->json(['status' => 'ok']);
})->name('test');

