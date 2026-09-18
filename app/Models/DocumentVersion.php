<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'thesis_chapter_id',
        'version_number',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'notes',
        'text_content',
        'uploader_id',
    ];

    protected $casts = [
        'version_number' => 'integer',
        'file_size' => 'integer',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(ThesisChapter::class, 'thesis_chapter_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function revisionItems(): HasMany
    {
        return $this->hasMany(RevisionItem::class);
    }

    public function formattedSize(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    public function downloadFilename(): string
    {
        $chapter = $this->chapter;
        $thesis = $chapter->thesis;
        $studentNim = $thesis->student->nim_nip ?? 'student';
        $ext = pathinfo($this->original_filename, PATHINFO_EXTENSION);
        return "Progresa_{$studentNim}_Bab{$chapter->order}_v{$this->version_number}.{$ext}";
    }
}
