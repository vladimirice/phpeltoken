<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class PublishTransactionCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('app:publish_transaction_command')
            ->setDescription('Publish new transaction to pool')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $walletService = $this->getContainer()->get('app.wallet_service');
        $blockchain = $this->getContainer()->get('app.blockchain');

        $helper = $this->getHelper('question');

        $wallets = $walletService->getWalletsIndexedByLogin();

        $logins = array_keys($wallets);

        $senderQuestion = new ChoiceQuestion(
            'Please select sender login',
            $logins
        );
        $senderQuestion->setErrorMessage('There is no such login: %s.');
        $senderLogin = $helper->ask($input, $output, $senderQuestion);

        $recipientQuestion = new ChoiceQuestion(
            'Please select recipient login',
            $logins
        );
        $recipientQuestion->setErrorMessage('There is no such login: %s.');
        $recipientLogin = $helper->ask($input, $output, $recipientQuestion);

        $amountQuestion = new Question('Please enter the amount of coins: ');
        $amount = (int) $helper->ask($input, $output, $amountQuestion);

        $output->writeln("{$senderLogin} is sending {$amount} coins to {$recipientLogin}");

        $senderWallet = $wallets[$senderLogin];
        $recipientWallet = $wallets[$recipientLogin];

        $blockchain->addTransactionToPool(
            $senderWallet,
            $recipientWallet,
            $amount
        );
    }
}
