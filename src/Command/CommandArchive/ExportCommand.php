<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Product\Listing;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(
    name: 'app:export',
    description: 'Export products!'
)]
class ExportCommand extends AbstractCommand
{
    
    protected function configure()
    {
        $this
            ->addArgument('marketplace', InputOption::VALUE_OPTIONAL, 'The marketplace to import from.')
            ->addOption('download', null, InputOption::VALUE_NONE, 'If set, Shopify listing data will always be downloaded.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
/*        self::$downloadFlag = $input->getOption('download');
        self::$marketplaceArg = $input->getArgument('marketplace');*/

        $products = new Listing();
        $products->setUnpublished(true);
        $products->setOrderKey('productIdentifier');
        $products->setOrder('ASC');

        $productList = $products->load();

        foreach ($productList as $product) {
            if ($product->getParent() instanceof Product) {
                continue;
            }
            $flag = false;
            foreach ($product->getChildren([Product::OBJECT_TYPE_OBJECT], true) as $child) {
                echo "{$child->getKey()}\n";
                $flag = true;
            }
            if (!$flag) {
                echo "{$product->getKey()}\n";
                echo "{$product->getKey()}\n";
                echo "{$product->getKey()}\n";
                echo "{$product->getKey()}\n";
            }
        }
        return Command::SUCCESS;
    }
}
