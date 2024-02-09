<?php

namespace App\Concerns;

trait HasEnumValues
{
    public static function values(): array
    {
        $cases = static::cases();

        return array_column($cases, 'value');
    }
}
