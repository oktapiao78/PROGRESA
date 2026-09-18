<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupervisionSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'supervisor_id',
        'date',
        'start_time',
        'end_time',
        'quota',
        'booked_count',
        'location_type',
        'room_or_link',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'quota' => 'integer',
        'booked_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(SupervisionRequest::class);
    }

    public function hasAvailableQuota(): bool
    {
        return $this->booked_count < $this->quota;
    }
}
