<?php

namespace AppBundle\Command;

use AppBundle\Model\Transaction;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExampleCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:example')
            ->setDescription('Very basic blockchain example')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $blockchain = $this->getContainer()->get('app.blockchain');
        $repo = $this->getContainer()->get('app.repository');

        $transactionToAlice = new Transaction($repo->getCreatorAddressHash(), $repo->getAliceAddressHash(), 10);
        $blockchain->addTransaction($transactionToAlice);
        $transactionToBob = new Transaction($repo->getCreatorAddressHash(), $repo->getBobAddressHash(), 10);
        $blockchain->addTransaction($transactionToBob);

        $st = microtime(1);
        $nextProof = $blockchain->generateNextProof();

        echo 'Mining time is (s): ' . (string) (microtime(1) - $st) . "\r\n";

        $blockchain->createNewBlockAndAddToChain($nextProof, $repo->getCreatorAddressHash());

        print_r($blockchain->getChain());
    }
}
