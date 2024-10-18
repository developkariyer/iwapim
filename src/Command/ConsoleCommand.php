<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:console',
    description: 'Interactive PimCore Console',
)]

class ConsoleCommand extends AbstractCommand
{
    protected function configure() 
    {
        $this->addOption('logging',null, InputOption::VALUE_NONE, 'Log everything to the log file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Pimcore Interactive Shell (REPL)');
        $context = [];

        while (true) {
            fwrite(STDOUT, "\nIWAPIM >>> ");
            $command = trim(fgets(STDIN));
            if (trim($command) === 'exit') {
                $io->success('Goodbye!');
                return 0;
            }
            try {
                extract($context);
                if (preg_match('/^echo\s+/', $command)) {
                    eval($command . ';');
                } else {
                    // Wrap other commands in return to capture result
                    $result = eval('return ' . $command . ';');

                    // Only output if result is non-null
                    if ($result !== null) {
                        $io->writeln(var_export($result, true));
                    }
                }
                $context = get_defined_vars();
            } catch (\Throwable $e) {
                $io->error($e->getMessage());
            }
        }
    }

}