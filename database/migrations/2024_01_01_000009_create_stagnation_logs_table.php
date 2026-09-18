<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stagnation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_id')->constrained('theses')->cascadeOnDelete();
            $table->unsignedInteger('days_inactive')->default(0);
            $table->timestamp('last_student_activity_at')->nullable();
            $table->timestamp('last_supervisor_activity_at')->nullable();
            $table->string('risk_level')->default('on_track')->index(); // on_track, low, medium, high, critical
            $table->text('notes')->nullable();
            $table->boolean('is_escalated')->default(false)->index();
            $table->timestamp('escalated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stagnation_logs');
    }
};
