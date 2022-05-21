<?php

namespace App\Service;

use App\Enum\Currency;
use App\Repository\RateRepository;
use App\ValueObject\Money;

class CurrencyService
{
    public const BASE = Currency::RUB;
    private RateRepository $rateRepository;

    public function __construct(RateRepository $rateRepository)
    {
        $this->rateRepository = $rateRepository;
    }

    public function rate(Money $amount, string $from, string $to): Money
    {
        if (self::BASE === $from) {
            $toRate = $this->rateRepository->findOneBy(['currency' => $to]);

            return Money::fromFloat($amount->getMinor() * $toRate->getValue());
        }

        $fromRate = $this->rateRepository->findOneBy(['currency' => $from]);

        return Money::fromFloat($amount->getMinor() / $fromRate->getValue());
    }
}
