<?php

namespace App\Support\Theme;

enum ThemePreference: string
{
    case Light = 'light';
    case Dark = 'dark';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $theme): string => $theme->value, self::cases());
    }

    public static function fallback(?string $value): self
    {
        return self::tryFrom((string) $value) ?? self::Light;
    }
}
