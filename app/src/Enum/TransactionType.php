<?php

namespace App\Enum;

use JetBrains\PhpStorm\ArrayShape;

class TransactionType
{
    public const DEBIT = 'debit';
    public const CREDIT = 'credit';

    #[ArrayShape(['DEBIT' => 'string', 'CREDIT' => 'string'])]
    public static function all(): array
    {
        return [
            'DEBIT' => self::DEBIT,
            'CREDIT' => self::CREDIT,
        ];
    }
}
