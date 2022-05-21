<?php

namespace App\Command;

use App\Service\ExchangeService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:rate:randomize',
    description: 'Add a short description for your command',
)]
class RateRandomizeCommand extends Command
{
    private ExchangeService $exchangeService;

    public function __construct(ExchangeService $exchangeService)
    {
        parent::__construct('app:rate:randomize');
        $this->exchangeService = $exchangeService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rate = $this->exchangeService->randomize();

        $output->writeln('Rate now = '.$rate->getValue());

        return Command::SUCCESS;
    }
}
