<?php 

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
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
        $this->addOption('logging', null, InputOption::VALUE_NONE, 'Log everything to the log file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Pimcore Interactive Shell (REPL)');
        $context = [];
        $logging = $input->getOption('logging');
        $logFile = PIMCORE_PROJECT_ROOT . '/var/log/console.log';

        while (true) {
            $command = $io->ask('PHP> ');

            if (trim($command) === 'exit') {
                $io->success('Goodbye!');
                return Command::SUCCESS;
            }

            try {
                extract($context);

                // Handle echo and return statements differently
                if (preg_match('/^echo\s+/', $command)) {
                    eval($command . ';');
                } else {
                    $result = eval('return ' . $command . ';');
                    if ($result !== null) {
                        $io->writeln(var_export($result, true));
                    }
                }

                // Capture the current context
                $context = get_defined_vars();

                // Optionally log the command and its result
                if ($logging) {
                    file_put_contents($logFile, "Command: $command\nResult: " . var_export($result, true) . "\n", FILE_APPEND);
                }

            } catch (\ParseError $e) {
                $io->error('Parse Error: ' . $e->getMessage());
            } catch (\Throwable $e) {
                $io->error('Error: ' . $e->getMessage());
            }
        }
    }
}
