<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('study_program_id')->constrained('study_programs')->cascadeOnDelete();
            $table->string('title');
            $table->text('abstract')->nullable();
            $table->string('academic_year', 20)->default('2025/2026');
            $table->foreignId('supervisor_1_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('supervisor_2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('in_progress')->index();
            $table->date('start_date')->nullable();
            $table->date('target_completion_date')->nullable();
            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theses');
    }
};
