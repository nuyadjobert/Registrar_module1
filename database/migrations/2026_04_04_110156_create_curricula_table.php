<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurriculaTable extends Migration
{
    public function up()
    {
        Schema::create('curricula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('year_level');
            $table->string('semester'); // 1,2,summer
            $table->string('school_year'); // e.g., 2025-2026
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['program_id','subject_id','year_level','semester','school_year'], 'curriculum_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('curricula');
    }
}