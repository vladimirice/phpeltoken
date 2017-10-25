<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitBlockchainCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('app:init_blockchain_command')
            ->setDescription('Initialize blockchain')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $walletService = $this->getContainer()->get('app.wallet_service');
        $blockchain = $this->getContainer()->get('app.blockchain');

        $creatorWallet = $walletService->getCreatorWallet();
        $blockchain->createBlockchain($creatorWallet);

        $output->writeln('Blockchain is created.');
    }
}
