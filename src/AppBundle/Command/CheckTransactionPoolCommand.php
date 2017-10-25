<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckTransactionPoolCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('app:check_transaction_pool_command')
            ->setDescription('Check transaction pool');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $walletService = $this->getContainer()->get('app.wallet_service');
        $blockchain = $this->getContainer()->get('app.blockchain');

        $transactionPool = $blockchain->getTransactionPool();

        $validTransactions =
            $blockchain->checkAndRewriteTransactionPool($walletService->getWalletsIndexedByAddress());

        $output->writeln('Transaction pool is checked and is rewritten');
        $output->writeln('Total transactions amount: ' . \count($transactionPool));
        $output->writeln('Valid transactions amount: ' . \count($validTransactions));
    }
}
