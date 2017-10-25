<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckBlockchainCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('app:check_blockchain_command')
            ->setDescription('Check blockchain');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $walletService = $this->getContainer()->get('app.wallet_service');
        $blockchain = $this->getContainer()->get('app.blockchain');

        $blockchain->checkBlockchain($walletService->getWalletsIndexedByAddress());

        $output->writeln('Blockchain is valid.');
    }
}
