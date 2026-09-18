<?php

namespace App\Models;

use App\Enums\SupervisionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupervisionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'supervision_slot_id',
        'thesis_id',
        'student_id',
        'supervisor_id',
        'topic',
        'student_notes',
        'status',
        'supervisor_notes',
        'meeting_summary',
        'action_items',
        'scheduled_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => SupervisionStatus::class,
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function slot(): BelongsTo
    {
        return $this->belongsTo(SupervisionSlot::class, 'supervision_slot_id');
    }

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
