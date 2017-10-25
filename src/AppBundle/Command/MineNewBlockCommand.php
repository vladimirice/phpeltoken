<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class MineNewBlockCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('app:mine_new_block_command')
            ->setDescription('Mine new block');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $blockchain = $this->getContainer()->get('app.blockchain');
        $walletService = $this->getContainer()->get('app.wallet_service');

        $helper = $this->getHelper('question');

        $wallets = $walletService->getWalletsIndexedByLogin();
        $logins = array_keys($wallets);

        $senderQuestion = new ChoiceQuestion(
            'Please select miner login',
            $logins
        );
        $senderQuestion->setErrorMessage('There is no such login: %s.');
        $minerLogin = $helper->ask($input, $output, $senderQuestion);

        $blockchain->mineNewBlock($wallets[$minerLogin], $walletService->getWalletsIndexedByAddress());

        $output->writeln("New block is mined by {$minerLogin}");
    }
}
