<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevisionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'thesis_chapter_id',
        'document_version_id',
        'supervisor_id',
        'note',
        'is_resolved',
        'student_response',
        'resolved_at',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(ThesisChapter::class, 'thesis_chapter_id');
    }

    public function documentVersion(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
