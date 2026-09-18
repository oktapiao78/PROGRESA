<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapter_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_program_id')->nullable()->constrained('study_programs')->nullOnDelete();
            $table->string('code')->index(); // e.g. BAB_1, BAB_2, etc.
            $table->string('title');        // e.g. Pendahuluan
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(1);
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_templates');
    }
};
