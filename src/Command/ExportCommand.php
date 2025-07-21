<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Product\Listing as ProductListing;
use App\Utils\Utility;

#[AsCommand(
    name: 'app:export',
    description: 'Export json'
)]

class ExportCommand extends AbstractCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
//        $productListing = new ProductListing();
//        $productListing->setLimit(10);
//        $productListing->setOffset(0);
//        $products = $productListing->load();
//        foreach ($products as $product) {
//            $level = $product->getProductLevel();
//            $productKey = $product->getKey();
//            echo  $productKey."\n";
//            echo  $level."\n";
//
//
//        }

        $mainProducts = $this->getMainProducts(3, 0);
        foreach ($mainProducts as $product) {
            $id = $product->getId();
            $name = $product->getName();
            $category = $product->getProductCategory();
            $identifier = $product->getProductIdentifier();
            $variationSizeList = $product->getVariationSizeList();
            $variationColorList = $product->getVariationColorList();
            $description = $product->getDescription();
            $image = $product->getImage()?->getFullPath() ?? '';
            $variants = $this->getVariantsProduct($identifier);

            echo $id."\n";
            echo $name."\n";
            echo $category."\n";
            echo $identifier."\n";
            echo $variationSizeList."\n";
            echo $variationColorList."\n";
            echo $description."\n";
            echo $image."\n";
            foreach ($variants as $variant) {
                $iwasku = $variant->getIwasku();
                $ean = $variant->getEanGtin();
                $asinMap = $this->getAsin($iwasku);

                echo $iwasku."\n";
                echo $ean."\n";
                echo "************************\n";
            }
            echo "========================\n";
        }

        return Command::SUCCESS;
    }

    private function getMainProducts($limit, $offset)
    {
        $mainProductListing = new ProductListing();
        $mainProductListing->setLimit($limit);
        $mainProductListing->setOffset($offset);
        $mainProductListing->setCondition("productLevel = 0");
        $mainProducts = $mainProductListing->load();
        return $mainProducts;
    }

    private function getVariantsProduct($productIdentifier)
    {
        $variantProductListing = new ProductListing();
        $variantProductListing->setCondition("productLevel = 1");
        $variantProductListing->setCondition("productIdentifier = '$productIdentifier'");
        return $variantProductListing->load();
    }

    private function getAsin($iwasku)
    {
        $sql = "SELECT asin, fnsku FROM iwa_inventory where iwasku = '$iwasku'";
        $result = Utility::fetchFromSql($sql);
        $asinMap = [];
        foreach ($result as $row) {
            $asin = $row['asin'];
            $fnsku = $row['fnsku'];
            if (!isset($asinMap[$asin])) {
                $asinMap[$asin] = [];
            }
            if (!in_array($fnsku, $asinMap[$asin])) {
                $asinMap[$asin][] = $fnsku;
            }
        }
        return $asinMap;
    }

}