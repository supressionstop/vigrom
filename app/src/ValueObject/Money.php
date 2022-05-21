<?php

namespace App\ValueObject;

class Money
{
    private int $amount;

    public function __construct(int $major)
    {
        $this->amount = $major;
    }

    public static function fromFloat(float $minor): self
    {
        $major = (int) round(($minor * 100));

        return new self($major);
    }

    public function getMajor(): int
    {
        return $this->amount;
    }

    public function getMinor(): float
    {
        return $this->amount / 100;
    }
}
