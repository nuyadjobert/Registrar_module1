<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('curricula', function (Blueprint $table) {
            $table->string('school_year')->default('2025-2026')->after('semester');
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('school_year');
        });
    }

    public function down(): void
    {
        Schema::table('curricula', function (Blueprint $table) {
            $table->dropColumn(['school_year', 'status']);
        });
    }
};