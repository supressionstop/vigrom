<?php

namespace App\Enum;

use JetBrains\PhpStorm\ArrayShape;

class Currency
{
    public const USD = 'USD';
    public const RUB = 'RUB';

    #[ArrayShape(['USD' => 'string', 'RUB' => 'string'])]
    public static function all(): array
    {
        return [
            'USD' => self::USD,
            'RUB' => self::RUB,
        ];
    }
}
