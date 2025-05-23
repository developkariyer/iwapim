<?php

namespace App\Command;

use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\ProductListingMessage;
use Symfony\Component\Messenger\Stamp\TransportNamesStamp;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputOption;
use App\Model\DataObject\Marketplace;
use App\Connector\Marketplace\CiceksepetiConnector;

#[AsCommand(
    name: 'app:autolisting',
    description: 'Outputs Hello, World!'
)]
class AutoListingCommand extends AbstractCommand
{
    public function __construct(private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    private array $marketplaceConfig = [
        'ciceksepeti' => 265384,
        'shopifycfwtr' => 84124,
    ];

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->syncShopifyCiceksepeti();
        return Command::SUCCESS;
    }

    private function syncShopifyCiceksepeti()
    {
        $cfwTrSql = "SELECT oo_id FROM object_query_varyantproduct WHERE marketplace__id = :marketplace_id";
        $ciceksepetiSql = "SELECT oo_id FROM object_query_varyantproduct WHERE sellerSku = :seller_sku AND marketplace__id = :marketplace_id";
        $cfwTrVariantProductsIds = Utility::fetchFromSql($cfwTrSql, ['marketplace_id' => $this->marketplaceConfig['shopifycfwtr']]);
        $productList = [];
        $toBeListedProducts = [];
        foreach ($cfwTrVariantProductsIds as $cfwTrVariantProductsId) {
            $shopifyProduct = VariantProduct::getById($cfwTrVariantProductsId['oo_id']);
            $mainProducts = $shopifyProduct->getMainProduct();
            if (!is_array($mainProducts) || empty($mainProducts)) {
                continue;
            }
            $mainProduct = $mainProducts[0];
            if ($mainProduct instanceof Product) {
                $iwasku = $mainProduct->getIwasku();
                $ciceksepetiProductsId = Utility::fetchFromSql($ciceksepetiSql, ['seller_sku' => $iwasku, 'marketplace_id' => $ciceksepetiMarketplaceId]);
                if (!is_array($ciceksepetiProductsId) || empty($ciceksepetiProductsId)) {
                    $toBeListedProducts[] = $mainProduct;
                }
                else {
                    $ciceksepetiProductId = $ciceksepetiProductsId[0];
                    $ciceksepetiProduct = VariantProduct::getById($ciceksepetiProductId['oo_id']);
                    if (!$ciceksepetiProduct instanceof VariantProduct) {
                        continue;
                    }
                    echo "Ciceksepeti product found for: $iwasku \n";
                    $preparedProduct = $this->prepareCiceksepetiProduct($ciceksepetiProduct, $shopifyProduct, $iwasku);
                    if ($preparedProduct) {
                        $productList[] = $preparedProduct;
                    }
                    if (count($productList) >= 200) {
                        $this->sendToCiceksepeti($productList);
                        $productList = [];
                    }
                }
            }
        }
        if (!empty($productList)) {
            $this->sendToCiceksepeti($productList);
        }
        if (!empty($toBeListedProducts)) {
            $this->createListingProcess($toBeListedProducts);
        }
    }

    private function createListingProcess($toBeListedProducts)
    {
        $groupedProducts = [];
        foreach ($toBeListedProducts as $mainProduct) {
            $parent = $mainProduct->getParent();
            if (!$parent) {
                continue;
            }
            $parentId = $parent->getId();
            $productId = $mainProduct->getId();
            if (!isset($groupedProducts[$parentId])) {
                $groupedProducts[$parentId] = [];
            }
            $groupedProducts[$parentId][] = $productId;
        }
        foreach ($groupedProducts as $parentId => $variantIds) {
            $message = new ProductListingMessage(
                'list',
                $parentId,
                265384,
                'admin',
                $variantIds,
                [],
                1,
                'test'
            );
            $stamps = [new TransportNamesStamp(['ciceksepeti'])];
            $this->bus->dispatch($message, $stamps);
        }
    }

    private function getShopifyImages($parentApiJsonShopify)
    {
        $images = [];
        $widthThreshold = 2000;
        $heightThreshold = 2000;
        if (isset($parentApiJsonShopify['media']['nodes'])) {
            foreach ($parentApiJsonShopify['media']['nodes'] as $node) {
                if (
                    isset($node['mediaContentType'], $node['preview']['image']['url'], $node['preview']['image']['width'], $node['preview']['image']['height']) &&
                    $node['mediaContentType'] === 'IMAGE' &&
                    ($node['preview']['image']['width'] < $widthThreshold || $node['preview']['image']['height'] < $heightThreshold)
                ) {
                    $images[] = $node['preview']['image']['url'];
                }
            }
        }
        return $images;
    }

    private function prepareCiceksepetiProduct(VariantProduct $ciceksepetiProduct, VariantProduct $shopifyProduct, $iwasku)
    {
        $parentApiJsonShopify = json_decode($shopifyProduct->jsonRead('parentResponseJson'), true);
        $apiJsonShopify = json_decode($shopifyProduct->jsonRead('apiResponseJson'), true);
        $apiJsonCiceksepeti = json_decode($ciceksepetiProduct->jsonRead('apiResponseJson'), true);
        $ciceksepetiIsActive = $apiJsonCiceksepeti['isActive'];
        if (!$ciceksepetiIsActive) {
            echo "Ciceksepeti product is not active: $iwasku \n";
            return null;
        }
        $images = $this->getShopifyImages($parentApiJsonShopify);
        if (empty($images)) {
            $images = $apiJsonCiceksepeti['images'] ?? [];
        }
        $cleanAttributes = [];
        if (isset($apiJsonCiceksepeti['attributes']) && is_array($apiJsonCiceksepeti['attributes'])) {
            foreach ($apiJsonCiceksepeti['attributes'] as $attr) {
                if (isset($attr['textLength']) && $attr['textLength'] == 0) {
                    $cleanAttributes[] = [
                        'ValueId' => $attr['id'],
                        'Id' => $attr['parentId'],
                        'textLength' => 0
                    ];
                }
            }
        }
        return [
            'productName' => mb_substr($shopifyProduct->getTitle(), 0, 255),
            'mainProductCode' => $apiJsonCiceksepeti['mainProductCode'],
            'stockCode' => $iwasku,
            'categoryId' => $apiJsonCiceksepeti['categoryId'],
            'description' => mb_substr($parentApiJsonShopify['descriptionHtml'], 0, 20000),
            'deliveryMessageType' => $apiJsonCiceksepeti['deliveryMessageType'],
            'deliveryType' => $apiJsonCiceksepeti['deliveryType'],
            'stockQuantity' => $apiJsonShopify['inventoryQuantity'],
            'salesPrice' => $apiJsonShopify['price'] * 1.5,
            'attributes' => $cleanAttributes,
            'isActive' => $parentApiJsonShopify['status'] === 'ACTIVE' ? 1 : 0,
            'images' => array_slice($images, 0, 5)
        ];
    }

    private function sendToCiceksepeti($productList)
    {
        $data = [
            'products' => $productList,
        ];
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $ciceksepetiConnector = new CiceksepetiConnector(Marketplace::getById(265384));
        $ciceksepetiConnector->updateProduct($json);
        echo "Sent " . count($productList) . " products to Ciceksepeti.\n";
    }

    private function searchProductAndReturnIds($productIdentifier)
    {
        $productSql = '
        SELECT oo_id, name, productCategory from object_query_product
        WHERE productIdentifier = :productIdentifier AND productLevel = 0
        LIMIT 1';
        $variantSql = '
        SELECT oo_id, iwasku, variationSize, variationColor FROM object_query_product
        WHERE productIdentifier = :productIdentifier AND productLevel = 1 AND listingItems IS NOT NULL';

        $product = Utility::fetchFromSql($productSql, ['productIdentifier' => $productIdentifier]);
        if (!is_array($product) || empty($product) || !isset($product[0]['oo_id'])) {
            return [];
        }
        $variants = Utility::fetchFromSql($variantSql, ['productIdentifier' => $productIdentifier]);
        if (!is_array($variants) || empty($variants)) {
            return [];
        }
        $productData = [
            'product_id' => $product[0]['oo_id']
        ];
        $variantData = [];
        foreach ($variants as $variant) {
            $variantData[] = $variant['oo_id'];
        }
        $productData['variantIds'] = $variantData;
        return $productData;
    }

}