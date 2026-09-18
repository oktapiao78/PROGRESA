<?php

namespace App\Enums;

enum ChapterStatus: string
{
    case NOT_STARTED = 'not_started';
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case IN_REVIEW = 'in_review';
    case REVISION_REQUIRED = 'revision_required';
    case APPROVED = 'approved';

    public function label(): string
    {
        return match($this) {
            self::NOT_STARTED => 'Belum Dimulai',
            self::DRAFT => 'Draft Mahasiswa',
            self::SUBMITTED => 'Diajukan',
            self::IN_REVIEW => 'Sedang Ditinjau',
            self::REVISION_REQUIRED => 'Perlu Revisi',
            self::APPROVED => 'Disetujui',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::NOT_STARTED => 'bg-slate-100 text-slate-600 border-slate-200',
            self::DRAFT => 'bg-sky-100 text-sky-700 border-sky-200',
            self::SUBMITTED => 'bg-blue-100 text-blue-700 border-blue-200',
            self::IN_REVIEW => 'bg-amber-100 text-amber-700 border-amber-200',
            self::REVISION_REQUIRED => 'bg-rose-100 text-rose-700 border-rose-200',
            self::APPROVED => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        };
    }
}
