<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThesisSupervisorAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'thesis_id', 'supervisor_id', 'role', 'assigned_by', 'assigned_at', 'ended_at', 'notes',
    ];

    protected $casts = [
        'role' => 'integer',
        'assigned_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function thesis(): BelongsTo { return $this->belongsTo(Thesis::class); }
    public function supervisor(): BelongsTo { return $this->belongsTo(User::class, 'supervisor_id'); }
    public function assignedBy(): BelongsTo { return $this->belongsTo(User::class, 'assigned_by'); }
}
