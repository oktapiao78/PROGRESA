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
            self::MAHASISWA => 'bg-sky-100 text-sky-800 border-sky-200',
            self::DOSEN => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            self::KOORDINATOR => 'bg-amber-100 text-amber-800 border-amber-200',
            self::KAPRODI => 'bg-purple-100 text-purple-800 border-purple-200',
            self::ADMIN => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }
}
