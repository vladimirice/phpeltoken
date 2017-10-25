<?php

namespace AppBundle\Command;

use AppBundle\Model\Blockchain\InputOutputDto;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowAvailableInputCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('app:show_available_input_command')
            ->setDescription('Show available input');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->renderInputsTable($output);
    }

    /**
     * @param OutputInterface $output
     */
    private function renderInputsTable(OutputInterface $output)
    {
        $walletService = $this->getContainer()->get('app.wallet_service');
        $balances = $walletService->getLoginToAvailableInputs();

        $inputsTable = new Table($output);
        $inputsTable
            ->setHeaders(['login', 'available inputs'])
        ;

        // #refactor
        /**
         * @var string $login
         * @var InputOutputDto[] $inputsArray
         */
        foreach ($balances as $login => $inputsArray) {
            $inputAmount = [];
            foreach ($inputsArray as $input) {
                $inputAmount[] = $input->getTransaction()->getAmount();
            }
            $inputsString = empty($inputsArray) ? '-' : implode('; ', $inputAmount);

            $inputsTable->addRow([$login, $inputsString]);
        }
        $inputsTable->render();
    }
}
