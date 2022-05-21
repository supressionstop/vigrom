<?php

namespace App\Service;

use App\Entity\Rate;
use App\Enum\Currency;
use Doctrine\ORM\EntityManagerInterface;

class ExchangeService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function randomize(string $currency = Currency::USD): Rate
    {
        $value = 1 / mt_rand(30, 150);

        $rate = $this->em->find(Rate::class, 1);
        if (null === $rate) {
            $rate = new Rate();
            $rate->setCurrency($currency);
        }
        $rate->setValue($value);
        $rate->setUpdatedAt(new \DateTime());

        $this->em->persist($rate);
        $this->em->flush();

        return $rate;
    }
}
