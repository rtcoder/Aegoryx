<?php

namespace App\Support\Localization;

enum Locale: string
{
    case Polish = 'pl';
    case English = 'en';
    case German = 'de';
    case Spanish = 'es';
    case Russian = 'ru';
    case French = 'fr';
    case Italian = 'it';
    case Czech = 'cs';
    case Slovak = 'sk';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
