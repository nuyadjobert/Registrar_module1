<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('department');
            $table->enum('type', ['Major', 'Minor', 'Elective', 'General Education'])->default('Major')->after('status');
            $table->text('description')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['status', 'type', 'description']);
        });
    }
};