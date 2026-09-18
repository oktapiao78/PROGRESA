<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChapterApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'thesis_chapter_id',
        'stage',
        'approver_id',
        'status',
        'feedback',
        'action_date',
    ];

    protected $casts = [
        'status' => ApprovalStatus::class,
        'action_date' => 'datetime',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(ThesisChapter::class, 'thesis_chapter_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function stageLabel(): string
    {
        return match($this->stage) {
            'pembimbing_1' => 'Dosen Pembimbing 1',
            'pembimbing_2' => 'Dosen Pembimbing 2',
            'koordinator' => 'Koordinator Tugas Akhir',
            default => ucfirst(str_replace('_', ' ', $this->stage)),
        };
    }
}
