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

        while (true) {
            fwrite(STDOUT, "\nIWAPIM >>> ");
            $command = trim(fgets(STDIN));
            if (trim($command) === 'exit') {
                $io->success('Goodbye!');
                return 0;
            }
            try {
                extract($context);
                $result = eval($command . ';');
                $context = get_defined_vars();
                if ($result !== null) {
                    $io->writeln(var_export($result, true));
                }
            } catch (\Throwable $e) {
                $io->error($e->getMessage());
            }
        }
    }

}