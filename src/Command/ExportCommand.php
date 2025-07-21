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
        $export = [];
        $mainProducts = $this->getMainProducts(3, 0);
        foreach ($mainProducts as $product) {
            $productData = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'category' => $product->getProductCategory(),
                'identifier' => $product->getProductIdentifier(),
                'variationSizeList' => $product->getVariationSizeList(),
                'variationColorList' => $product->getVariationColorList(),
                'description' => $product->getDescription(),
                'image' => $product->getImage()?->getFullPath() ?? ''
            ];
            $productData['variants'] = [];
            foreach ($this->getVariantsProduct($product->getProductIdentifier()) as $variant) {
                $productData['variants'][] = [
                    'iwasku' => $variant->getIwasku(),
                    'ean' => $variant->getEanGtin(),
                    'asinMap' => $this->getAsin($variant->getIwasku()),
                    'productWidth' => $variant->getProductDimension1(),
                    'productHeight' => $variant->getProductDimension2(),
                    'productLength' => $variant->getProductDimension3(),
                    'productWeight' => $variant->getProductWeight(),
                    'packageWidth' => $variant->getPackageDimension1(),
                    'packageHeight' => $variant->getPackageDimension2(),
                    'packageLength' => $variant->getPackageDimension3(),
                    'packageWeight' => $variant->getPackageWeight(),
                    'desi5000' => $variant->getDesi5000(),
                ];
            }
            $export[] = $productData;
        }
        echo json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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
        $variantProductListing->setCondition("productLevel = 1 AND productIdentifier = '$productIdentifier'");
        return $variantProductListing->load();
    }

    private function getAsin($iwasku)
    {
        $sql = "SELECT asin, fnsku FROM iwa_inventory where iwasku = '$iwasku' and iwasku is not null";
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