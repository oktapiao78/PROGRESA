<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapter_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_chapter_id')->constrained('thesis_chapters')->cascadeOnDelete();
            $table->string('stage', 50); // pembimbing_1, pembimbing_2, koordinator
            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending')->index();
            $table->text('feedback')->nullable();
            $table->timestamp('action_date')->nullable();
            $table->timestamps();

            $table->unique(['thesis_chapter_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_approvals');
    }
};
