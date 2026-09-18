<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thesis_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_id')->constrained('theses')->cascadeOnDelete();
            $table->foreignId('chapter_template_id')->nullable()->constrained('chapter_templates')->nullOnDelete();
            $table->string('title');
            $table->unsignedInteger('order')->default(1);
            $table->string('status')->default('not_started')->index();
            $table->unsignedBigInteger('current_version_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thesis_chapters');
    }
};
