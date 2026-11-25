<?php

namespace App\Helpers;

class RomanHelper
{
    public static function toRoman(int|string|null $number): string
    {
        $n = (int) $number;
        $map = [
            1000 => 'M', 900 => 'CM', 500 => 'D', 400 => 'CD',
            100 => 'C', 90 => 'XC', 50 => 'L', 40 => 'XL',
            10 => 'X', 9 => 'IX', 5 => 'V', 4 => 'IV', 1 => 'I'
        ];
        $result = '';
        foreach ($map as $val => $roman) {
            while ($n >= $val) {
                $result .= $roman;
                $n -= $val;
            }
        }
        return $result ?: (string) $number;
    }
}
