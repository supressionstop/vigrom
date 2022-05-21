<?php

namespace App\Command;

use App\Entity\Rate;
use App\Entity\User;
use App\Entity\Wallet;
use App\Enum\Currency;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:init',
    description: 'Basic init for showcase.',
)]
class InitCommand extends Command
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $em;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em)
    {
        parent::__construct('app:init-users');
        $this->passwordHasher = $passwordHasher;
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $alice = new User();
        $alice->setEmail('alice@mail.com');
        $alicePassword = $this->passwordHasher->hashPassword($alice, 'alice_123');
        $alice->setPassword($alicePassword);
        $aliceWallet = new Wallet();
        $aliceWallet->setCurrency(Currency::RUB);
        $aliceWallet->setOwner($alice);
        $alice->setWallet($aliceWallet);

        $bob = new User();
        $bob->setEmail('bob@mail.com');
        $bob->setRoles(['ROLE_ADMIN']);
        $bobPassword = $this->passwordHasher->hashPassword($alice, 'bob_123');
        $bob->setPassword($bobPassword);
        $bobWallet = new Wallet();
        $bobWallet->setCurrency(Currency::USD);
        $bobWallet->setOwner($bob);
        $bob->setWallet($bobWallet);

        $rate = new Rate();
        $rate->setValue(0.016982);
        $rate->setCurrency(Currency::USD);

        $this->em->persist($alice);
        $this->em->persist($aliceWallet);
        $this->em->persist($bob);
        $this->em->persist($bobWallet);
        $this->em->flush();

        return Command::SUCCESS;
    }
}
