<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateSampleTransactionsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('app:create_sample_transactions_command')
            ->setDescription('Create sample transactions');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $walletService = $this->getContainer()->get('app.wallet_service');
        $blockchain = $this->getContainer()->get('app.blockchain');

        $wallets = $walletService->getWalletsIndexedByLogin();

        $blockchain->addTransactionToPool(
            $wallets['creator'],
            $wallets['alice'],
            50
        );

        $walletsByAddress = $walletService->getWalletsIndexedByAddress();

        $blockchain->mineNewBlock($walletService->getBobWallet(), $walletsByAddress);

        $blockchain->addTransactionToPool(
            $wallets['alice'],
            $wallets['anna'],
            50
        );

        $blockchain->mineNewBlock($walletService->getCreatorWallet(), $walletsByAddress);

        $blockchain->addTransactionToPool(
            $wallets['creator'],
            $wallets['charlie'],
            25
        );
        $blockchain->mineNewBlock($walletService->getAliceWallet(), $walletsByAddress);

        $output->writeln('Sample blockchain transactions are created.');
    }
}
