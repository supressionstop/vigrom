<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Exception\InsufficientFundsException;
use App\Repository\TransactionRepository;
use App\Repository\WalletRepository;
use App\Service\TransactionService;
use Doctrine\ORM\PessimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/wallet', name: 'wallet_')]
class WalletController extends AbstractController
{
    #[Route('/{walletId}', name: 'balance', methods: ['GET'])]
    public function currentBalance(int $walletId, WalletRepository $walletRepository): Response
    {
        $wallet = $walletRepository->findOneBy(['id' => $walletId]);

        if (null === $wallet) {
            return $this->json(['error' => sprintf('Wallet not found by ID=%d', $walletId)], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $wallet], Response::HTTP_OK, [], [AbstractNormalizer::GROUPS => ['balance']]);
    }

    #[Route('/{walletId}', name: 'do_transaction', methods: ['POST'])]
    public function transaction(int $walletId, Request $request, SerializerInterface $serializer, WalletRepository $walletRepository, ValidatorInterface $validator, TransactionService $transactionService): Response
    {
        $wallet = $walletRepository->findOneBy(['id' => $walletId]);
        if (null === $wallet) {
            $this->json(['errors' => sprintf('Wallet not found by ID=%d', $walletId)], Response::HTTP_NOT_FOUND);
        }

        /** @var Transaction $transaction */
        $transaction = $serializer->deserialize(
            $request->getContent(),
            Transaction::class,
            'json'
        );
        $transaction->setWallet($wallet);

        $violations = $validator->validate($transaction);
        if ($violations->count() > 0) {
            return $this->json(['errors' => $violations], Response::HTTP_BAD_REQUEST);
        }

        try {
            $updatedWallet = $transactionService->transact($wallet, $transaction);
        } catch (InsufficientFundsException $insufficientFundsException) {
            return $this->json(['errors' => $insufficientFundsException->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (PessimisticLockException $pessimisticLockException) {
            return $this->json(['errors' => 'Wallet is currently updating by someone. Try again.'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['data' => $updatedWallet], Response::HTTP_OK, [], [AbstractNormalizer::GROUPS => ['balance']]);
    }

    #[Route('/{walletId}/log', name: 'get_transactions_log', methods: ['GET'])]
    public function log(int $walletId, Request $request, WalletRepository $walletRepository, TransactionRepository $transactionRepository): Response
    {
        $wallet = $walletRepository->findOneBy(['id' => $walletId]);

        if (null === $wallet) {
            return $this->json(['error' => sprintf('Wallet not found by ID=%d', $walletId)], Response::HTTP_NOT_FOUND);
        }

        if ($request->query->get('last_week_refunds')) {
            $transactions = $transactionRepository->getLastWeekRefunds();

            return $this->json(['data' => $transactions], Response::HTTP_OK, [], [AbstractNormalizer::GROUPS => ['public']]);
        }

        $transactions = $wallet->getTransactions();

        return $this->json(['data' => $transactions], Response::HTTP_OK, [], [AbstractNormalizer::GROUPS => ['public']]);
    }
}
