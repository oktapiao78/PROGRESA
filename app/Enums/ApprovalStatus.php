<?php

namespace App\Enums;

enum ApprovalStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REVISION_REQUESTED = 'revision_requested';
    case REJECTED = 'rejected';
    case ESCALATED = 'escalated';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Approval',
            self::APPROVED => 'Disetujui',
            self::REVISION_REQUESTED => 'Permintaan Revisi',
            self::REJECTED => 'Ditolak',
            self::ESCALATED => 'Tereskalasi ke Koordinator',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border-amber-200',
            self::APPROVED => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::REVISION_REQUESTED => 'bg-rose-100 text-rose-800 border-rose-200',
            self::REJECTED => 'bg-red-100 text-red-800 border-red-200',
            self::ESCALATED => 'bg-purple-100 text-purple-800 border-purple-200',
        };
    }
}
