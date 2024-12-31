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
        $iwasku = Registry::getKey("B0B51XB71B",'asin-to-iwasku');
        if (isset($iwasku)) {
            $product = Product::findByField('iwasku',$iwasku);
            if ($product instanceof Product) {
                echo $product->getInheritedField('productCode') . "\n";
                echo $product->getInheritedField('productCategory') . "\n";
                echo $product->getInheritedField('imageUrl') . "\n";
                if ($product->getInheritedField('sticker4x6eu')) {
                    echo "Sticker exists\n";
                    echo $product->getInheritedField('sticker4x6eu'). "\n";
                }
                else {
                    echo "Sticker does not exist\n";
                    $sticker = $product->checkSticker4x6eu();
                    $stickerPath = $sticker->getFullPath();
                    echo  $stickerPath. "\n";
                }

            } else {
                print_r('No product found');
            }
        }
        $this->writeInfo($iwasku);

        // Output "Hello, World!" as green text

        // Return success status code
        return Command::SUCCESS;
    }
}
