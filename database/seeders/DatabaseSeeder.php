<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // User - won't duplicate
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name'  => 'Test User']
        );

        // Subjects - won't duplicate
        $subjects = [
            ['subject_code' => 'CS101', 'subject_name' => 'Introduction to Computing',     'units' => 3],
            ['subject_code' => 'CS102', 'subject_name' => 'Computer Programming 1',         'units' => 3],
            ['subject_code' => 'CS103', 'subject_name' => 'Computer Programming 2',         'units' => 3],
            ['subject_code' => 'CS104', 'subject_name' => 'Data Structures and Algorithms', 'units' => 3],
            ['subject_code' => 'CS105', 'subject_name' => 'Database Management Systems',    'units' => 3],
            ['subject_code' => 'GE101', 'subject_name' => 'Mathematics in Modern World',    'units' => 3],
            ['subject_code' => 'GE102', 'subject_name' => 'Purposive Communication',        'units' => 3],
            ['subject_code' => 'GE103', 'subject_name' => 'The Contemporary World',         'units' => 3],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(
                ['subject_code' => $subject['subject_code']],
                $subject
            );
        }

        // Students - won't duplicate
        Student::firstOrCreate(
            ['student_number' => '2024-0001'],
            [
                'name'   => 'Juan Dela Cruz',
                'course' => 'BSIT'
            ]
        );
    }
}