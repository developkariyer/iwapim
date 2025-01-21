<?php

namespace App\Command;

use App\Utils\Utility;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:remove-nums',
    description: 'Remove numbered folders!'
)]
class RemoveNumberedFoldersCommand extends AbstractCommand
{
    /**
     * @throws DuplicateFullPathException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $urunler = Utility::checkSetPath('Ürünler');
        $productListingObject = new Product\Listing();
        $products = $productListingObject->load();
        $index = 0;
        $total = count($products);
        $correctPath = $wrongPath = 0;
        foreach ($products as $product) {
            $index++;
            echo "Processing $index/$total Correct: $correctPath Wrong: $wrongPath                 \r";
            if ($product->level()==1) {
                continue;
            }
            $parent = $product->getParent();
            $grandParent = $parent->getParent();
            if ($grandParent === $urunler) {
                $correctPath++;
            } else {
                $wrongPath++;
            }
        }

        return Command::SUCCESS;
    }
}
