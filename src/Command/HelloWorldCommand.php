<?php

namespace App\Command;

use App\Model\DataObject\VariantProduct;
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
        $iwasku = Registry::getKey("B08VKMV227",'asin-to-iwasku');
        if (isset($iwasku)) {
            $variant = VariantProduct::findOneByField('iwasku',$iwasku);
            if ($variant instanceof VariantProduct) {
                print_r($variant->getIwasku());
            } else {
                print_r('No variant found');
            }
        }
        $this->writeInfo($iwasku);

        // Output "Hello, World!" as green text

        // Return success status code
        return Command::SUCCESS;
    }
}
