<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervision_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supervision_slot_id')->nullable()->constrained('supervision_slots')->nullOnDelete();
            $table->foreignId('thesis_id')->constrained('theses')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete();
            $table->string('topic');
            $table->text('student_notes')->nullable();
            $table->string('status')->default('requested')->index();
            $table->text('supervisor_notes')->nullable();
            $table->text('meeting_summary')->nullable(); // Catatan Berita Acara Bimbingan
            $table->text('action_items')->nullable();   // Tindak lanjut / target sebelum bimbingan berikutnya
            $table->dateTime('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervision_requests');
    }
};
