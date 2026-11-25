<?php

namespace App\Models;

use App\Enums\ChapterStatus;
use App\Enums\ThesisStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thesis extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'study_program_id',
        'title',
        'abstract',
        'academic_year',
        'supervisor_1_id',
        'supervisor_2_id',
        'status',
        'start_date',
        'target_completion_date',
        'progress_percentage',
    ];

    protected $casts = [
        'status' => ThesisStatus::class,
        'start_date' => 'date',
        'target_completion_date' => 'date',
        'progress_percentage' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function supervisor1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_1_id');
    }

    public function supervisor2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_2_id');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(ThesisChapter::class)->orderBy('order');
    }

    public function supervisionRequests(): HasMany
    {
        return $this->hasMany(SupervisionRequest::class);
    }

    public function supervisorAssignments(): HasMany
    {
        return $this->hasMany(ThesisSupervisorAssignment::class);
    }

    public function stagnationLogs(): HasMany
    {
        return $this->hasMany(StagnationLog::class)->latest();
    }

    public function latestStagnation(): HasOne
    {
        return $this->hasOne(StagnationLog::class)->latestOfMany();
    }

    public function isSupervisor(int|User $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $this->supervisor_1_id === $userId || $this->supervisor_2_id === $userId;
    }

    public function recalculateProgress(): int
    {
        $total = $this->chapters()->count();
        if ($total === 0) {
            $this->update(['progress_percentage' => 0]);
            return 0;
        }

        $approved = $this->chapters()->where('status', ChapterStatus::APPROVED)->count();
        $percentage = (int) round(($approved / $total) * 100);

        $status = $this->status;
        if ($percentage === 100) {
            $status = ThesisStatus::DEFENSE_READY;
        } elseif ($percentage >= 60 && in_array($this->status, [ThesisStatus::DRAFT, ThesisStatus::PROPOSAL])) {
            $status = ThesisStatus::IN_PROGRESS;
        }

        $this->update([
            'progress_percentage' => $percentage,
            'status' => $status,
        ]);

        return $percentage;
    }
}
