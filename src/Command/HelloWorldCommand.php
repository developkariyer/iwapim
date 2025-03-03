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
    function getReturnsFiles($dir) {
        $returnsFiles = [];
        foreach (glob($dir . '*/', GLOB_ONLYDIR) as $marketplaceDir) {
            $returnsFilePath = $marketplaceDir . 'RETURNS.json';

            if (file_exists($returnsFilePath)) {
                $returnsFiles[$marketplaceDir] = json_decode(file_get_contents($returnsFilePath), true);
            }
        }
        return $returnsFiles;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $directory = PIMCORE_PROJECT_ROOT . '/tmp/marketplaces/';
        $returnsData = getReturnsFiles($directory);
        print_r($returnsData);

        // Output "Hello, World!" as green text
       // $this->writeInfo("Hello, World!", $output);

        // Return success status code
        return Command::SUCCESS;
    }
}
