<?php

namespace App\Enums;

enum ThesisStatus: string
{
    case DRAFT = 'draft';
    case PROPOSAL = 'proposal';
    case IN_PROGRESS = 'in_progress';
    case REVISION = 'revision';
    case SEMINAR_READY = 'seminar_ready';
    case DEFENSE_READY = 'defense_ready';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft Judul',
            self::PROPOSAL => 'Penyusunan Proposal',
            self::IN_PROGRESS => 'Bimbingan Berjalan',
            self::REVISION => 'Dalam Revisi',
            self::SEMINAR_READY => 'Siap Seminar Hasil',
            self::DEFENSE_READY => 'Siap Sidang Akhir',
            self::COMPLETED => 'Selesai / Lulus',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::DRAFT => 'bg-slate-100 text-slate-700 border-slate-200',
            self::PROPOSAL => 'bg-blue-100 text-blue-700 border-blue-200',
            self::IN_PROGRESS => 'bg-amber-100 text-amber-700 border-amber-200',
            self::REVISION => 'bg-orange-100 text-orange-700 border-orange-200',
            self::SEMINAR_READY => 'bg-indigo-100 text-indigo-700 border-indigo-200',
            self::DEFENSE_READY => 'bg-purple-100 text-purple-700 border-purple-200',
            self::COMPLETED => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        };
    }
}
