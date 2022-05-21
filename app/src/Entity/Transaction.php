<?php

namespace App\Entity;

use App\Enum\Currency;
use App\Enum\Reason;
use App\Enum\TransactionType;
use App\Repository\TransactionRepository;
use App\ValueObject\Money;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['public'])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Choice(callback: [TransactionType::class, 'all'])]
    #[Groups(['public'])]
    private string $type;

    /** Major */
    #[ORM\Column(type: 'integer')]
    #[Assert\Type(type: 'numeric')]
    #[Assert\NotEqualTo(value: 0)]
    #[Groups(['public'])]
    private int $amount;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Choice(callback: [Currency::class, 'all'])]
    #[Groups(['public'])]
    private string $currency;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Choice(callback: [Reason::class, 'all'])]
    #[Groups(['public'])]
    private string $reason;

    #[ORM\ManyToOne(targetEntity: Wallet::class, inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private Wallet $wallet;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['public'])]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): self
    {
        $this->wallet = $wallet;

        return $this;
    }

    public function getAmountAsMoney(): Money
    {
        return new Money($this->getAmount());
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
