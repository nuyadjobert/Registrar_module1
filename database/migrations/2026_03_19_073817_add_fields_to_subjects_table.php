<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->enum('type', ['Lecture', 'Laboratory', 'Lecture & Lab'])->default('Lecture')->after('units');
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['type', 'status']);
        });
    }
};