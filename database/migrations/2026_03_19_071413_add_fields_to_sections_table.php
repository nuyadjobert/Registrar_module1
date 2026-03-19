<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->string('school_year')->default('2025-2026')->after('capacity');
            $table->enum('semester', ['1st Semester', '2nd Semester', 'Summer'])->default('1st Semester')->after('school_year');
            $table->enum('status', ['Open', 'Closed', 'Full'])->default('Open')->after('semester');
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn(['school_year', 'semester', 'status']);
        });
    }
};