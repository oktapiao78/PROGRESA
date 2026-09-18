<?php

namespace App\Models;

use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StagnationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'thesis_id',
        'days_inactive',
        'last_student_activity_at',
        'last_supervisor_activity_at',
        'risk_level',
        'notes',
        'is_escalated',
        'escalated_at',
    ];

    protected $casts = [
        'days_inactive' => 'integer',
        'last_student_activity_at' => 'datetime',
        'last_supervisor_activity_at' => 'datetime',
        'risk_level' => RiskLevel::class,
        'is_escalated' => 'boolean',
        'escalated_at' => 'datetime',
    ];

    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }
}
