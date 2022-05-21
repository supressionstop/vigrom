<?php

namespace App\Dto;

class TransactionDto
{
    private string $type;

    private float $amount;

    private string $currency;

    private string $reason;

    public function __construct(string $type, float $amount, string $currency, string $reason)
    {
        $this->type = $type;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->reason = $reason;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}
