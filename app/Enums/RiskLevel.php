<?php

namespace App\Enums;

enum RiskLevel: string
{
    case ON_TRACK = 'on_track';
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';

    public function label(): string
    {
        return match($this) {
            self::ON_TRACK => 'Lancar / Tepat Waktu',
            self::LOW => 'Risiko Rendah',
            self::MEDIUM => 'Risiko Sedang (Perhatian)',
            self::HIGH => 'Risiko Tinggi (Stagnan)',
            self::CRITICAL => 'Kritis (Terancam DO/Mundur)',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::ON_TRACK => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::LOW => 'bg-blue-100 text-blue-800 border-blue-200',
            self::MEDIUM => 'bg-amber-100 text-amber-800 border-amber-200',
            self::HIGH => 'bg-orange-100 text-orange-800 border-orange-200',
            self::CRITICAL => 'bg-red-100 text-red-800 border-red-200',
        };
    }
}
