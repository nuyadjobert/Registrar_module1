<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // GET /api/courses
    public function index()
    {
        $courses = Course::all();

        return response()->json([
            'message' => 'Courses retrieved successfully',
            'data'    => $courses
        ]);
    }

    // POST /api/courses
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_code' => 'required|unique:courses',
            'course_name' => 'required|string',
            'units'       => 'required|integer',
            'department'  => 'required|string',
        ]);

        $course = Course::create($validated);

        return response()->json([
            'message' => 'Course created successfully',
            'data'    => $course
        ], 201);
    }

    // GET /api/courses/{id}
    public function show($id)
    {
        $course = Course::findOrFail($id);

        return response()->json([
            'message' => 'Course retrieved successfully',
            'data'    => $course
        ]);
    }

    // PUT /api/courses/{id}
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'course_code' => 'sometimes|unique:courses,course_code,' . $id,
            'course_name' => 'sometimes|string',
            'units'       => 'sometimes|integer',
            'department'  => 'sometimes|string',
        ]);

        $course->update($validated);

        return response()->json([
            'message' => 'Course updated successfully',
            'data'    => $course
        ]);
    }

    // DELETE /api/courses/{id}
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully'
        ]);
    }
}