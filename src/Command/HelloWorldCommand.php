<?php

namespace App\Command;

use App\Message\CiceksepetiCategoryUpdateMessage;
use App\Message\TestMessage;
use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:hello-world',
    description: 'Outputs Hello, World!'
)]
class HelloWorldCommand extends AbstractCommand
{
    public function __construct(private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $marketplaceId = 265384;

        $message = new CiceksepetiCategoryUpdateMessage($marketplaceId);
        $this->bus->dispatch($message, ['async' => true]);
        //$this->bus->dispatch(new TestMessage("Selam, bu kuyruğa gitti!"));
        echo "Mesaj kuyruğa atıldı.";

        /*$categoryUpdateCheckSql = "SELECT updated_at FROM `iwa_ciceksepeti_category_attributes` WHERE category_id = :category_id limit 1";
        $result = Utility::fetchFromSql($categoryUpdateCheckSql, ['category_id' => 1512312056]);
        if ($result && isset($result[0]['updated_at'])) {
            $updatedAtTimestamp = strtotime($result[0]['updated_at']);
            $nowTimestamp = time();

            $diffInSeconds = $nowTimestamp - $updatedAtTimestamp;
            $diffInDays = $diffInSeconds / (60 * 60 * 24);

            if ($diffInDays >= 1) {
                // 1 günden eski → işlem yapılmalı
                echo "Kategori güncel değil, işlem yapılacak.";
            } else {
                // Güncel
                echo "Kategori zaten güncel.";
            }
        } else {
            // Kayıt yok → işlem yapılmalı
            echo "Kategori daha önce işlenmemiş, işlem yapılacak.";
        }*/
        /*$ciceksepetiConnector = new CiceksepetiConnector(Marketplace::getById(265384));
        $ciceksepetiConnector->downloadCategories();
        $sql = "SELECT oo_id FROM `object_query_varyantproduct` WHERE marketplaceType = 'Ciceksepeti'";
        $ciceksepetiVariantIds = Utility::fetchFromSql($sql);
        if (!is_array($ciceksepetiVariantIds) || empty($ciceksepetiVariantIds)) {
            return [];
        }
        $categoryIdList = [];
        foreach ($ciceksepetiVariantIds as $ciceksepetiVariantId) {
            $variantProduct = VariantProduct::getById($ciceksepetiVariantId['oo_id']);
            if (!$variantProduct instanceof VariantProduct) {
                continue;
            }
            $apiData = json_decode($variantProduct->jsonRead('apiResponseJson'), true);
            $categoryIdList[] = $apiData['categoryId'];
        }
        $categoryIdList = array_unique($categoryIdList);
        foreach ($categoryIdList as $categoryId) {
            $ciceksepetiConnector->getCategoryAttributesAndSaveDatabase($categoryId);
            echo ".\n";
        }*/


        /*$sql = "SELECT oo_id FROM `object_query_varyantproduct` WHERE marketplaceType = 'Ciceksepeti'";
        $ciceksepetiVariantIds = Utility::fetchFromSql($sql);
        $ciceksepetiVariant = [];
        $categoryIdList = [];
        foreach ($ciceksepetiVariantIds as $ciceksepetiVariantId) {
            $variantProduct = VariantProduct::getById($ciceksepetiVariantId['oo_id']);
            if (!$variantProduct instanceof VariantProduct) {
                continue;
            }
            $apiData = json_decode($variantProduct->jsonRead('apiResponseJson'), true);
            $categoryIdList[] = $apiData['categoryId'];
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
                'categoryId' => $apiData['categoryId']
            ];
        }
        $categoryIdList = array_unique($categoryIdList);
        $categoryIdString = implode(',', array_map('intval', $categoryIdList));

        $sqlCategory = "SELECT id, category_name FROM iwa_ciceksepeti_categories WHERE id IN ($categoryIdString)";
        $categories = Utility::fetchFromSql($sqlCategory);

        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[$cat['id']] = $cat['category_name'];
        }

        $grouped = [];
        foreach ($ciceksepetiVariant as $listing) {
            $categoryId = $listing['categoryId'];
            $mainCode = $listing['mainProductCode'] ?? 'unknown';
            $categoryName = $categoryMap[$categoryId] ?? 'Bilinmeyen Kategori';

            $grouped[$categoryName][$mainCode][] = $listing;
        }

        print_r($grouped);*/




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
