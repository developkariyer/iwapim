<?php

namespace App\Command;

use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Model\DataObject\GroupProduct;

#[AsCommand(
    name: 'app:hello-world',
    description: 'Outputs Hello, World!'
)]
class HelloWorldCommand extends AbstractCommand
{
    private string $sqlPath = PIMCORE_PROJECT_ROOT . '/src/SQL/Sticker/';
    /**
     * @throws Exception
     * @throws DuplicateFullPathException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        // Output "Hello, World!" as green text
       // $this->writeInfo("Hello, World!", $output);

        // Return success status code
        return Command::SUCCESS;
    }
}
