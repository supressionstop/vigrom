<?php

namespace App\Enum;

class Reason
{
    public const STOCK = 'stock';
    public const REFUND = 'refund';

    public static function all(): array
    {
        return [
            'STOCK' => self::STOCK,
            'REFUND' => self::REFUND,
        ];
    }
}
