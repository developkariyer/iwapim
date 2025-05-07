<?php

namespace App\Command;

use App\Message\CiceksepetiCategoryUpdateMessage;
use App\Message\TestMessage;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use phpseclib3\File\ASN1\Maps\AttributeValue;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\ProductListingMessage;
use Symfony\Component\Messenger\Stamp\TransportNamesStamp;
use Symfony\Component\HttpClient\HttpClient;

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

    public function getCiceksepetiListingCategoriesIdList(): array
    {
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
        return array_unique($categoryIdList);
    }



    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = "CA-001A";
        $productSql = '
        SELECT oo_id, name, productCategory from object_query_product
        WHERE productIdentifier = :productIdentifier AND productLevel = 0
        LIMIT 1';
        $variantSql = '
        SELECT oo_id, iwasku, variationSize, variationColor FROM object_query_product
        WHERE productIdentifier = :productIdentifier AND productLevel = 1 AND listingItems IS NOT NULL';

        $product = Utility::fetchFromSql($productSql, ['productIdentifier' => $identifier]);

        $variants = Utility::fetchFromSql($variantSql, ['productIdentifier' => $identifier]);

        $productData = [
            'id' => $product[0]['oo_id'],
            'name' => $product[0]['name'],
            'productCategory' => $product[0]['productCategory']
        ];
        $variantData = [];
        foreach ($variants as $variant) {
            $variantData[] = [
                'id' => $variant['oo_id'],
                'iwasku' => $variant['iwasku'],
                'variationSize' => $variant['variationSize'],
                'variationColor' => $variant['variationColor']
            ];
        }
        $productData['variants'] = $variantData;

        print_r($productData);

        // IJ
       // $productId = 238133;
        //$variantIds = [240430, 240431, 240433, 240434];
        //240439, 240440, 240442, 240443

        // CA-41
        //$productId = 154770;
        //$variantIds = [155464, 155462, 155468, 155466, 155434, 155432, 155437, 155435];

        /*$ciceksepetiMessage = new ProductListingMessage(
            'list',
            $productId,
            265384,
            'ciceksepetiUser',
            $variantIds,
            [],
            1,
            'test'
        );
        $stamps = [new TransportNamesStamp(['ciceksepeti'])];
        $this->bus->dispatch($ciceksepetiMessage, $stamps);
        echo "Istek CICEKSEPETI kuyruğuna gönderildi.\n";*/







        /*$hepsiburadaMessage = new ProductListingMessage(
            'unlist',
            123221145,
            12223,
            'hepsiburadaUser',
            [1,2],
            [],
            1,
            'live'
        );
        $stamps = [new TransportNamesStamp(['hepsiburada'])];
        $this->bus->dispatch($hepsiburadaMessage, $stamps);
        echo "Istek HEPSIBURADA kuyruğuna gönderildi.\n";

        $hepsiburadaMessage2 = new ProductListingMessage(
            'unlist',
            123221145,
            12223,
            'hepsiburadaUser2',
            [1,2],
            [],
            0,
            'live'
        );
        $stamps = [new TransportNamesStamp(['hepsiburada'])];
        $this->bus->dispatch($hepsiburadaMessage2, $stamps);
        echo "Istek HEPSIBURADA2 kuyruğuna gönderildi.\n";


        $trendyolMessage = new ProductListingMessage(
            'list',
            12345,
            123,
            'trendyolUser',
            [1,2,3],
            [],
            0,
            'test'
        );
        $stamps = [new TransportNamesStamp(['trendyol'])];
        $this->bus->dispatch($trendyolMessage, $stamps);
        echo "\nIstek TRENDYOL kuyruğuna gönderildi.\n";


        $ciceksepetiMessage = new ProductListingMessage(
            'update_price',
            555555,
            33333,
            'ciceksepetiUser',
            [0],
            [],
            -1,
            'live'
        );
        $stamps = [new TransportNamesStamp(['ciceksepeti'])];
        $this->bus->dispatch($ciceksepetiMessage, $stamps);
        echo "Istek CICEKSEPETI kuyruğuna gönderildi.\n";*/






















        /*$marketplaceId = 265384;
        $message = new CiceksepetiCategoryUpdateMessage($marketplaceId);
        $this->bus->dispatch($message);
        //$this->bus->dispatch(new TestMessage("Selam, bu kuyruğa gitti!"));
        echo "Mesaj kuyruğa atıldı.";*/
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
