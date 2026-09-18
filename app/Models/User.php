<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'nim_nip',
        'role',
        'phone',
        'study_program_id',
        'avatar',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
        'is_active' => 'boolean',
    ];

    /**
     * Scope query to find user by identifier (email, NIM, or NIP)
     */
    public function scopeFindByIdentifier($query, string $identifier)
    {
        $trimmed = trim($identifier);

        return $query->where(function ($q) use ($trimmed) {
            $q->whereRaw('LOWER(email) = ?', [strtolower($trimmed)])
              ->orWhere('nim_nip', $trimmed);
        });
    }

    /**
     * Accessor for nim (convenience for mahasiswa)
     */
    public function getNimAttribute(): ?string
    {
        return $this->isMahasiswa() ? $this->nim_nip : null;
    }

    /**
     * Accessor for nip (convenience for lecturers and staff)
     */
    public function getNipAttribute(): ?string
    {
        return ! $this->isMahasiswa() ? $this->nim_nip : null;
    }

    /**
     * Accessor for status (maps boolean is_active to string status)
     */
    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'active' : 'inactive';
    }

    public function isMahasiswa(): bool
    {
        return $this->role === UserRole::MAHASISWA;
    }

    public function isDosen(): bool
    {
        return $this->role === UserRole::DOSEN;
    }

    public function isKoordinator(): bool
    {
        return $this->role === UserRole::KOORDINATOR;
    }

    public function isKaprodi(): bool
    {
        return $this->role === UserRole::KAPRODI;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function hasRole(UserRole|array|string $roles): bool
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
            return false;
        }

        $expectedRole = $roles instanceof UserRole ? $roles->value : $roles;
        return $this->role->value === $expectedRole;
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function studentThesis(): HasOne
    {
        return $this->hasOne(Thesis::class, 'student_id');
    }

    public function supervisedThesesAs1(): HasMany
    {
        return $this->hasMany(Thesis::class, 'supervisor_1_id');
    }

    public function supervisedThesesAs2(): HasMany
    {
        return $this->hasMany(Thesis::class, 'supervisor_2_id');
    }

    public function allSupervisedTheses()
    {
        return Thesis::where('supervisor_1_id', $this->id)
            ->orWhere('supervisor_2_id', $this->id);
    }

    public function supervisionSlots(): HasMany
    {
        return $this->hasMany(SupervisionSlot::class, 'supervisor_id');
    }

    public function supervisionRequests(): HasMany
    {
        return $this->hasMany(SupervisionRequest::class, 'student_id');
    }

    public function supervisorRequests(): HasMany
    {
        return $this->hasMany(SupervisionRequest::class, 'supervisor_id');
    }
}
