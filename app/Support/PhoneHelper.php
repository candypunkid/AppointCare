<?php

namespace App\Support;

class PhoneHelper
{
    /**
     * Normalize a phone number to E.164 (defaults to Nepal +977).
     * Accepts: "+9779812345678", "00977-98-1234-5678", "9841234567", "01-4412345".
     */
    public static function normalizeToE164(?string $phone, string $countryCode = '977'): ?string
    {
        $phone = trim((string) $phone);
        if ($phone === '') {
            return null;
        }

        $hasPlus = str_starts_with($phone, '+');
        $digits = preg_replace('/\D+/', '', $phone);
        if ($digits === '') {
            return null;
        }

        if ($hasPlus || str_starts_with($digits, '00')) {
            return '+'.preg_replace('/^00/', '', $digits);
        }

        if (str_starts_with($digits, $countryCode)) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '0')) {
            return '+'.$countryCode.substr($digits, 1);
        }

        return '+'.$countryCode.$digits;
    }
}
