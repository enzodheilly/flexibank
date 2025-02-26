<?php

// src/Command/UpdateCardOrderStatusCommand.php

namespace App\Command;

use App\Service\CardOrderStatusUpdater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:update-card-orders')]
class UpdateCardOrderStatusCommand extends Command
{
    private CardOrderStatusUpdater $cardOrderStatusUpdater;

    public function __construct(CardOrderStatusUpdater $cardOrderStatusUpdater)
    {
        parent::__construct();
        $this->cardOrderStatusUpdater = $cardOrderStatusUpdater;
    }

    protected function configure()
    {
        $this->setDescription('Met à jour automatiquement les statuts des commandes de cartes.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->cardOrderStatusUpdater->updateOrderStatus();
        $output->writeln('Statuts des commandes mis à jour avec succès.');
        return Command::SUCCESS;
    }
}
