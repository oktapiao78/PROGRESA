<?php

namespace App\Enums;

enum SupervisionStatus: string
{
    case REQUESTED = 'requested';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case RESCHEDULED = 'rescheduled';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::REQUESTED => 'Menunggu Konfirmasi',
            self::ACCEPTED => 'Disetujui / Terjadwal',
            self::REJECTED => 'Ditolak',
            self::RESCHEDULED => 'Dijadwalkan Ulang',
            self::COMPLETED => 'Selesai / Terlaksana',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::REQUESTED => 'bg-amber-100 text-amber-800 border-amber-200',
            self::ACCEPTED => 'bg-blue-100 text-blue-800 border-blue-200',
            self::REJECTED => 'bg-rose-100 text-rose-800 border-rose-200',
            self::RESCHEDULED => 'bg-purple-100 text-purple-800 border-purple-200',
            self::COMPLETED => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::CANCELLED => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }
}
