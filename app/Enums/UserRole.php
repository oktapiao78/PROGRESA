<?php

namespace App\Enums;

enum UserRole: string
{
    case MAHASISWA = 'mahasiswa';
    case DOSEN = 'dosen';
    case KOORDINATOR = 'koordinator';
    case KAPRODI = 'kaprodi';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match($this) {
            self::MAHASISWA => 'Mahasiswa',
            self::DOSEN => 'Dosen Pembimbing',
            self::KOORDINATOR => 'Koordinator TA',
            self::KAPRODI => 'Ketua Program Studi',
            self::ADMIN => 'Administrator',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::MAHASISWA => 'bg-blue-500/20 text-blue-400',
            self::DOSEN => 'bg-emerald-500/20 text-emerald-400',
            self::KOORDINATOR => 'bg-amber-500/20 text-amber-400',
            self::KAPRODI => 'bg-purple-500/20 text-purple-400',
            self::ADMIN => 'bg-rose-500/20 text-rose-400',
        };
    }
}