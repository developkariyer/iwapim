<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Connector\Wisersell\Connector;

#[AsCommand(
    name: 'app:console',
    description: 'Interactive PimCore Console',
)]
class ConsoleCommand extends AbstractCommand
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $t = new Connector();
        $io = new SymfonyStyle($input, $output);
        $io->title('IWAPIM Interactive Shell');
        $context = [];

        while (true) {
            // Ask for user input in the REPL
            $command = $io->ask('');
            
            // Exit the REPL loop
            if (trim($command) === 'exit') {
                $io->success('Goodbye!');
                return 0;
            }
            
            try {
                // Start capturing output
                ob_start();

                // Evaluate the command
                $result = eval($command . ';');

                // Capture any printed output
                $outputCaptured = ob_get_clean();

                // Print captured output from echo/print commands
                if (!empty($outputCaptured)) {
                    $io->writeln($outputCaptured);
                }

                // If the command has a return value, display it
                if ($result !== null) {
                    $io->writeln(var_export($result, true));
                }

                // Update context with new variables
                $context = get_defined_vars();

            } catch (\Throwable $e) {
                // Display errors
                $io->error($e->getMessage());
            }
        }
    }
}
