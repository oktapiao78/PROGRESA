<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use App\Enums\ChapterStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ThesisChapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'thesis_id',
        'chapter_template_id',
        'title',
        'order',
        'status',
        'current_version_id',
    ];

    protected $casts = [
        'status' => ChapterStatus::class,
        'order' => 'integer',
    ];

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ChapterTemplate::class, 'chapter_template_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class)->orderByDesc('version_number');
    }

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class, 'current_version_id');
    }

    public function revisionItems(): HasMany
    {
        return $this->hasMany(RevisionItem::class)->latest();
    }

    public function openRevisions(): HasMany
    {
        return $this->hasMany(RevisionItem::class)->where('is_resolved', false);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ChapterApproval::class);
    }

    public function approvalForStage(string $stage): ?ChapterApproval
    {
        return $this->approvals()->where('stage', $stage)->first();
    }

    public function isStageApproved(string $stage): bool
    {
        $approval = $this->approvalForStage($stage);
        return $approval && $approval->status === ApprovalStatus::APPROVED;
    }

    public function isFullyApproved(): bool
    {
        $p1 = $this->isStageApproved('pembimbing_1');
        $hasP2 = (bool) $this->thesis->supervisor_2_id;
        $p2 = $hasP2 ? $this->isStageApproved('pembimbing_2') : true;

        return $p1 && $p2;
    }
}
