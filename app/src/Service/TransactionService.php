<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\Wallet;
use App\Exception\InsufficientFundsException;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PessimisticLockException;
use Psr\Log\LoggerInterface;

class TransactionService
{
    private EntityManagerInterface $em;
    private CurrencyService $currencyService;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $em, CurrencyService $currencyService, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->currencyService = $currencyService;
        $this->logger = $logger;
    }

    /**
     * @throws InsufficientFundsException
     * @throws PessimisticLockException
     */
    public function transact(Wallet $wallet, Transaction $transaction): Wallet
    {
        $this->em->beginTransaction();
        try {
            $this->em->lock($wallet, LockMode::PESSIMISTIC_WRITE);

            $transaction->setWallet($wallet);

            if ($wallet->getCurrency() !== $transaction->getCurrency()) {
                $amount = $this->currencyService->rate(
                    $transaction->getAmountAsMoney(),
                    $transaction->getCurrency(),
                    $wallet->getCurrency()
                );
            } else {
                $amount = $transaction->getAmountAsMoney();
            }

            $wallet->sum($amount);
            $this->logger->critical($amount->getMajor());
            $this->logger->critical($amount->getMinor());

            $transaction->setWallet($wallet);
            $this->em->persist($wallet);
            $this->em->persist($transaction);
            $this->em->flush();
            $this->em->commit();
        } catch (PessimisticLockException $pessimisticLockException) {
            $this->em->rollback();
            $this->logger->alert('Lock happened!', [$pessimisticLockException, $wallet]);
            throw $pessimisticLockException;
        }

        return $wallet;
    }
}
