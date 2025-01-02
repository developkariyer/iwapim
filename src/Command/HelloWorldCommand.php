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
#[AsCommand(
    name: 'app:hello-world',
    description: 'Outputs Hello, World!'
)]
class HelloWorldCommand extends AbstractCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $product = Product::findByField('iwasku', 'IA03100EZ9QH');
        echo $product->getInheritedField('imageUrl')  . '\n'  ;


        // Output "Hello, World!" as green text
       // $this->writeInfo("Hello, World!", $output);

        // Return success status code
        return Command::SUCCESS;
    }
}
