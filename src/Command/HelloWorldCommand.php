<?php

namespace App\Command;

use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\VariantProduct\Listing;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sql = "SELECT oo_id FROM `object_query_varyantproduct` WHERE marketplaceType = 'Ciceksepeti'";
        $ciceksepetiVariantIds = Utility::fetchFromSql($sql);
        $ciceksepetiVariant = [];
        foreach ($ciceksepetiVariantIds as $ciceksepetiVariantId) {
            $variantProduct = VariantProduct::getById($ciceksepetiVariantId['oo_id']);
            if (!$variantProduct instanceof VariantProduct) {
                continue;
            }
            $apiData = json_decode($variantProduct->jsonRead('apiResponseJson'), true);
            $ciceksepetiVariant[] = [
                'link' => $apiData['link'],
                'images' => $apiData['images'],
                'barcode' => $apiData['barcode'],
                'variantIsActive' => $apiData['isActive'],
                'listPrice' => $apiData['listPrice'],
                'stockCode' => $apiData['stockCode'],
                'attributes' => $apiData['attributes'],
                'salesPrice' => $apiData['salesPrice'],
                'description' => $apiData['description'],
                'productCode' => $apiData['productCode'],
                'productName' => $apiData['productName'],
                'deliveryType' => $apiData['deliveryType'],
                'stockQuantity' => $apiData['stockQuantity'],
                'commissionRate' => $apiData['commissionRate'],
                'mainProductCode' => $apiData['mainProductCode'],
                'numberOfFavorites' => $apiData['numberOfFavorites'],
                'productIsActive' => $apiData['productStatusType'],
                'deliveryMessageType' => $apiData['deliveryMessageType'],
            ];
        }
        $grouped = [];
        foreach ($ciceksepetiVariant as $listing) {
            $mainCode = $listing['mainProductCode'] ?? 'unknown';
            $grouped[$mainCode][] = $listing;
        }
        print_r($grouped);







        // youtube video : https://www.youtube.com/watch?v=LhNG8MujVf0
        /*$mainProduct = Product::getById(238119); //main product
        $productName = $mainProduct->getProductIdentifier();
        $variationProducts = $mainProduct->getChildren([AbstractObject::OBJECT_TYPE_OBJECT], true); //variant products
        foreach ($variationProducts as $variationProduct) {
            if (!$variationProduct instanceof Product) {
                continue;
            }
            $listingItems = $variationProduct->getListingItems(); //listing items
            if (empty($listingItems)) {
                continue;
            }
            $iwasku = $variationProduct->getIwasku();
            $variationSize = $variationProduct->getVariationSize();
            $variationColor = $variationProduct->getVariationColor();
            $eanGtin = $variationProduct->getEanGtin();
            echo "iwasku: " . $iwasku . "\n";
            echo "variationSize: " . $variationSize . "\n";
            echo "variationColor: " . $variationColor . "\n";
            echo "eanGtin: " . $eanGtin . "\n";
            foreach ($listingItems as $listingItem) { // 1 tane listing seçilmesi lazım
                if (!$listingItem instanceof VariantProduct) {
                    continue;
                }
                echo $listingItem->getTitle() . "\n";
                $imageGallery = $listingItem->getImageGallery();
                //image jsondan ayır http lazım.
            }
        }*/





        return Command::SUCCESS;
    }
}
