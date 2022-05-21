<?php

namespace App\Entity;

use App\Exception\InsufficientFundsException;
use App\Repository\WalletRepository;
use App\ValueObject\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
class Wallet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['public'])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['public', 'balance'])]
    private string $currency;

    #[ORM\Column(type: 'integer')]
    #[Groups(['public', 'balance'])]
    private int $amount = 0;

    #[ORM\OneToOne(inversedBy: 'wallet', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    #[ORM\OneToMany(mappedBy: 'wallet', targetEntity: Transaction::class)]
    private Collection $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @throws InsufficientFundsException
     */
    public function sum(Money $money): void
    {
        $result = $this->amount + $money->getMajor();

        if ($result < 0) {
            throw new InsufficientFundsException('Insufficient funds in wallet.');
        }

        $this->amount = $result;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }
}
