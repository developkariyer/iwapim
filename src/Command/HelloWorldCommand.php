<?php

namespace App\Command;

use Pimcore\Model\DataObject\Product;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Utils\Utility;
use App\Utils\Registry;
use Pimcore\Model\DataObject\GroupProduct;

#[AsCommand(
    name: 'app:hello-world',
    description: 'Outputs Hello, World!'
)]
class HelloWorldCommand extends AbstractCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $gproduct = new GroupProduct\Listing();
        $result = $gproduct->load();
        foreach ($result as $item) {
            print_r($result);
        }


        // Output "Hello, World!" as green text
       // $this->writeInfo("Hello, World!", $output);

        // Return success status code
        return Command::SUCCESS;
    }
}
