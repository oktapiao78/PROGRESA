<?php

namespace App\Enums;

enum UserRole: string
{
    case MAHASISWA = 'mahasiswa';
    case DOSEN = 'dosen';

    public function label(): string
    {
        return match($this) {
            self::MAHASISWA => 'Mahasiswa',
            self::DOSEN => 'Dosen Pembimbing',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::MAHASISWA => 'bg-blue-500/20 text-blue-400',
            self::DOSEN => 'bg-emerald-500/20 text-emerald-400',
        };
    }
}
