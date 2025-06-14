<?php

namespace App\Connector\Marketplace;

use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Model\Element\DuplicateFullPathException;
use Random\RandomException;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CiceksepetiConnector extends MarketplaceConnectorAbstract
{
    private static array $apiUrl = [
        'offers' => "Products/",
        'updateInventoryPrice' => "Products/price-and-stock/",
        'batchStatus' => "Products/batch-status/",
        'orders' => "Order/GetOrders/",
        'returns' => "Order/getcanceledorders/",
        'categories' => "Categories/"
    ];

    public static string $marketplaceType = 'Ciceksepeti';

    /**
     * @throws \Exception
     */
    public function __construct($marketplace)
    {
        parent::__construct($marketplace);
        $this->httpClient = ScopingHttpClient::forBaseUri($this->httpClient, 'https://apis.ciceksepeti.com/api/v1/', [
            'headers' => [
                'x-api-key' => $this->marketplace->getCiceksepetiApiKey(),
                'Content-Type' => 'application/json'
            ],
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RandomException
     */
    public function download(bool $forceDownload = false): void
    {
        if (!$forceDownload && $this->getListingsFromCache()) {
            echo "Using cached listings\n";
            return;
        }
        $page = 1;
        $size = 60;
        $this->listings = [];
        do {
            $response = $this->httpClient->request('GET',static::$apiUrl['offers'],
                [
                    'query' =>
                    [
                        'Page' => $page,
                        'PageSize' => $size
                    ]
                ]
            );
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                echo "Error: $statusCode\n";
                break;
            }
            $data = $response->toArray();
            $products = $data['products'];
            $this->listings = array_merge($this->listings, $products);
            $totalItems = $data['totalCount'];
            echo "Page: " . $page . " ";
            echo "Count: " . count($this->listings) . " / Total Count: " . $totalItems . "\n";
            $page++;
            sleep(5);
        } while (count($this->listings) < $totalItems);
        if (empty($this->listings)) {
            echo "Failed to download listings\n";
            return;
        }
        $this->putListingsToCache();
    }

    /**
     * @throws DuplicateFullPathException
     * @throws \Exception
     */
    public function import($updateFlag, $importFlag): void
    {
        if (empty($this->listings)) {
            echo "Nothing to import\n";
        }
        $marketplaceFolder = Utility::checkSetPath(
            Utility::sanitizeVariable($this->marketplace->getKey(), 190),
            Utility::checkSetPath('Pazaryerleri')
        );
        $total = count($this->listings);
        $index = 0;
        foreach ($this->listings as $listing) {
            echo "($index/$total) Processing Listing {$listing['barcode']}:{$listing['productName']} ...";
            $parent = Utility::checkSetPath($marketplaceFolder);
            if ($listing['mainProductCode']) {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable($listing['mainProductCode']),
                    $parent
                );
            }
            VariantProduct::addUpdateVariant(
                variant: [
                    'imageUrl' => Utility::getCachedImage($listing['images'][0]) ?? '',
                    'urlLink' =>  $this->getUrlLink($listing['link']) ?? '',
                    'salePrice' => $listing['salesPrice'] ?? 0,
                    'saleCurrency' =>  $this->marketplace->getCurrency(),
                    'title' => $listing['productName'] ?? '',
                    'quantity' => $listing['stockQuantity'] ?? 0,
                    'attributes' => $listing['variantName']  ?? '',
                    'uniqueMarketplaceId' =>  $listing['stockCode'] ?? '',
                    'apiResponseJson' => json_encode($listing, JSON_PRETTY_PRINT),
                    'published' => $listing['productStatusType'] === 'YAYINDA' ? 1 : 0,
                    'sku' => $listing['stockCode'] ?? '',
                ],
                importFlag: $importFlag,
                updateFlag: $updateFlag,
                marketplace: $this->marketplace,
                parent: $parent
            );
            echo "OK\n";
            $index++;
        }    
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function downloadOrders(): void
    {
        $now = date('Y-m-d');
        try {
            $sqlLastUpdatedAt = "
                SELECT COALESCE(
                DATE_FORMAT(MAX(STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderModifyDate')), '%d/%m/%Y')), '%Y-%m-%d'),
                DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 3 MONTH), '%Y-%m-%d')) AS lastUpdatedAt
                FROM iwa_marketplace_orders
                WHERE marketplace_id = :marketplace_id;";
            $result = Utility::fetchFromSql($sqlLastUpdatedAt, [
                'marketplace_id' => $this->marketplace->getId()
            ]);
            $lastUpdatedAt = $result[0]['lastUpdatedAt'];
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
        echo "Last Updated At: $lastUpdatedAt\n";
        if ($lastUpdatedAt) {
            $threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));
            $startDate = max($threeMonthsAgo, $lastUpdatedAt); 
        } else {
            $startDate = date('Y-m-d', strtotime('-3 months')); 
        }
        $modifiedStartDate = date('Y-m-d', strtotime('+2 weeks', strtotime($startDate)));
        $endDate = ($modifiedStartDate < $now) ? $modifiedStartDate : $now;
        $pageSize = 100;
        echo "Last Updated At: $lastUpdatedAt\n";
        echo "Start Date: $startDate\n";
        echo "End Date: $endDate\n";
        if ($startDate === $endDate) {
            echo "No orders to download\n";
            return;
        }
        do {
            $page = 0;
            do {
                $response = $this->httpClient->request('POST', static::$apiUrl['orders'], ['json' => ['startDate' => $startDate, 'endDate' => $endDate, 'page' => $page, 'pageSize' => $pageSize]]);
                $statusCode = $response->getStatusCode();
                if ($statusCode !== 200) {
                    echo "Error: $statusCode\n";
                    return;
                }
                try {
                    $data = $response->toArray();
                    $orders = $data['supplierOrderListWithBranch'];
                    foreach ($orders as $order) {
                        $sqlInsertMarketplaceOrder = "
                            INSERT INTO iwa_marketplace_orders (marketplace_id, order_id, json) 
                            VALUES (:marketplace_id, :order_id, :json) ON DUPLICATE KEY UPDATE json = VALUES(json)";
                        Utility::executeSql($sqlInsertMarketplaceOrder, [
                            'marketplace_id' => $this->marketplace->getId(),
                            'order_id' => $order['orderId'],
                            'json' => json_encode($order)
                        ]);
                    }    
                } catch (\Exception $e) {
                    echo "Error: " . $e->getMessage() . "\n";
                }
                $page++;
                $totalElements = $data['orderListCount'];
                $totalPages = $data['pageCount'];
                $count = count($orders);
                echo "-----------------------------\n";
                echo "Total Elements: $totalElements\n"; 
                echo "Total Pages: $totalPages\n";
                echo "Current Page: $page\n"; 
                echo "Items on this page: $count\n";
                echo "Date Range: " . $startDate . " - " . $endDate . "\n"; 
                echo "-----------------------------\n";
                sleep(5);
            }while($page < $totalPages);
            $startDate = $endDate;
            $endDateCandidate = date('Y-m-d', strtotime($startDate . ' +2 weeks'));
            $endDate = ($endDateCandidate < $now) ? $endDateCandidate : $now;
            if ($startDate >= $now) {
                break;
            }
        }while($startDate < $now);
    }
    
    public function downloadInventory(): void
    {
        print_r(json_encode($this->getBatchRequestResult("6d502ff9-678a-40d3-abc6-5370832f96e9")));

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

    private function passiveVariant()
    {
        $variantProductIds = [286942];
        $updateCiceksepetiList = [];
        foreach ($variantProductIds as $variantProductId) {
            $ciceksepetiVariantProduct = VariantProduct::getById($variantProductId);
            $apiJsonCiceksepeti = json_decode($ciceksepetiVariantProduct->jsonRead('apiResponseJson'), true);
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
            $updateCiceksepetiList['products'][] = [
                'productName' => $apiJsonCiceksepeti['productName'],
                'mainProductCode' => $apiJsonCiceksepeti['mainProductCode'],
                'stockCode' => $apiJsonCiceksepeti['stockCode'],
                'categoryId' => $apiJsonCiceksepeti['categoryId'],
                'description' => $apiJsonCiceksepeti['description'],
                'deliveryMessageType' => $apiJsonCiceksepeti['deliveryMessageType'],
                'deliveryType' => $apiJsonCiceksepeti['deliveryType'],
                'stockQuantity' => 0,
                'salesPrice' => $apiJsonCiceksepeti['salesPrice'] * 1.5,
                'Attributes' => $cleanAttributes,
                'isActive' => 0,
                'images' => $apiJsonCiceksepeti['images']
            ];
        }
        $json = json_encode($updateCiceksepetiList, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $this->updateProduct($json);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RandomException
     */
    public function downloadReturns(): void
    {
        $page = 0;
        $pageSize = 50;
        $allReturns = [];
        do {
            $response = $this->httpClient->request('POST', static::$apiUrl['returns'], ['json' => ['page' => $page, 'pageSize' => $pageSize]]);
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                echo "Error: $statusCode\n";
                return;
            }
            $data = $response->toArray();
            $returns = $data['supplierOrderListWithBranch'] ?? [];
            $allReturns = array_merge($allReturns,$returns);
            $page++;
            $pageCount = $data['pageCount'];
            echo "Count: " . count($allReturns) . "Page: " . $page . "\n";
            sleep(5);
        } while ($page <= $pageCount);
        $this->putToCache('RETURNS.json', $allReturns);
    }

    /**
     * @throws RandomException
     */
    public function downloadCategories(): void
    {
        $categories = $this->getFromCache('categories.json');
        if (!$categories) {
            $response = $this->httpClient->request('GET',static::$apiUrl['categories']);
            $this->putToCache('categories.json', $response->toArray());
            $categories = $this->getFromCache('categories.json');
        }
        $this->processCategoriesAndSaveDatabase($categories['categories']);
    }

    public function processCategoriesAndSaveDatabase($categories, $path = '')
    {
        foreach ($categories as $category) {
            if (!isset($category['id']) || !isset($category['name'])) {
                continue;
            }
            $currentPath = $path ? $path . ' | ' . $category['name'] : $category['name'];
            if (empty($category['subCategories'])) {
                $id = $category['id'];
                //echo "$currentPath (ID: $id)\n";
                $sql = "INSERT INTO iwa_ciceksepeti_categories (id, category_name)
                    VALUES (:id, :category_name)
                    ON DUPLICATE KEY UPDATE
                        category_name = VALUES(category_name)";
                Utility::executeSql($sql, ['id' => $id, 'category_name' => $currentPath]);
            }
            if (!empty($category['subCategories'])) {
                $this->processCategoriesAndSaveDatabase($category['subCategories'], $currentPath);
            }
        }
    }

    public function getCategoryAttributesAndSaveDatabase($categoryId): void
    {
        //Attribute Check
        $categoryUpdateCheckSql = "SELECT updated_at FROM `iwa_ciceksepeti_category_attributes` WHERE category_id = :category_id limit 1";
        $result = Utility::fetchFromSql($categoryUpdateCheckSql, ['category_id' => $categoryId]);
        if ($result && isset($result[0]['updated_at'])) {
            $updatedAtTimestamp = strtotime($result[0]['updated_at']);
            $nowTimestamp = time();
            $diffInSeconds = $nowTimestamp - $updatedAtTimestamp;
            $diffInDays = $diffInSeconds / (60 * 60 * 24);
            if ($diffInDays < 1) {
                return;
            }
        }

        $attributeSql = "INSERT INTO iwa_ciceksepeti_category_attributes (attribute_id, category_id, attribute_name, is_required, varianter, type)
                         VALUES (:attribute_id, :category_id, :attribute_name, :is_required, :varianter, :type)
                         ON DUPLICATE KEY UPDATE
                         attribute_name = VALUES(attribute_name),
                         is_required = VALUES(is_required),
                         varianter = VALUES(varianter),
                         type = VALUES(type)";

        $response = $this->httpClient->request('GET', static::$apiUrl['categories'] . $categoryId . '/attributes');
        if ($response->getStatusCode() !== 200) {
            echo "Error: " . $response->getStatusCode();
            return;
        }

        $responseArray = $response->toArray();
        $categoryId = $responseArray['categoryId'];
        $attributeValueRows = [];
        foreach ($responseArray['categoryAttributes'] as $attribute) {
            if (!isset($attribute['attributeValues'])) {
                continue;
            }
            if ($attribute['attributeName'] == 'Marka') {
                continue;
            }
            // Save Attributes
            Utility::executeSql($attributeSql, [
                'attribute_id' => $attribute['attributeId'],
                'category_id' => $categoryId,
                'attribute_name' => $attribute['attributeName'],
                'is_required' => $attribute['required'],
                'varianter' => $attribute['varianter'],
                'type' => $attribute['type']
            ]);
            foreach ($attribute['attributeValues'] as $attributeValue) {
                $attributeValueRows[] = [
                    $attributeValue['id'],
                    $attribute['attributeId'],
                    $attributeValue['name']
                ];
            }
        }
        // BULK UPDATE
        if (!empty($attributeValueRows)) {
            $placeholders = [];
            $bindings = [];
            foreach ($attributeValueRows as $row) {
                $placeholders[] = '(?, ?, ?)';
                $bindings = array_merge($bindings, $row);
            }

            $attributeValueSql = "
                INSERT INTO iwa_ciceksepeti_category_attributes_values (attribute_value_id, attribute_id, name)
                VALUES " . implode(', ', $placeholders) . "
                ON DUPLICATE KEY UPDATE name = VALUES(name)";

            Utility::executeSql($attributeValueSql, $bindings);
        }
    }

    /**
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     * @throws RedirectionExceptionInterface
     * @throws TransportExceptionInterface
     * @throws RandomException
     */
    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null, $locationId = null): void
    {
        $stockCode = json_decode($listing->jsonRead('apiResponseJson'), true)['stockCode'];
        if (empty($stockCode)) {
            echo "Failed to get inventory item id for {$listing->getKey()}\n";
            return;
        }
        $body = [
            'items' => [
                [
                    'stockCode' => $stockCode,
                    'StockQuantity' => $targetValue,
                ]
            ]
        ];
        $response = $this->httpClient->request('PUT', static::$apiUrl['updateInventoryPrice'], ['body' => json_encode($body)]);
        sleep(1);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        $data = $response->toArray();
        $combinedData = [
            'inventory' => $data,
            'batchRequestResult' => $this->getBatchRequestResult($data['batchId'])
        ];
        echo "Inventory set\n";
        $filename = "SETINVENTORY_{$stockCode}.json";
        print_r($combinedData);
        $this->putToCache($filename, ['requeest'=>$body, 'response'=>$combinedData]);
    }

    /**
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     * @throws RedirectionExceptionInterface
     * @throws TransportExceptionInterface
     * @throws RandomException
     */
    public function setPrice(VariantProduct $listing, string $targetPrice, $targetCurrency = null, $sku = null, $country = null): void
    {
//        if (empty($targetPrice)) {
//            echo "Error: Price cannot be empty\n";
//            return;
//        }
//        if ($targetCurrency === null) {
//            $targetCurrency = $listing->getSaleCurrency();
//        }
//        $finalPrice = $this->convertCurrency($targetPrice, $targetCurrency, $listing->getSaleCurrency());
//        if (empty($finalPrice)) {
//            echo "Error: Currency conversion failed\n";
//            return;
//        }
        $stockCode = json_decode($listing->jsonRead('apiResponseJson'), true)['stockCode'];
        if (empty($stockCode)) {
            echo "Failed to get inventory item id for {$listing->getKey()}\n";
            return;
        }
        $body = [
            'items' => [
                [
                    'stockCode' => $stockCode,
                    'salesPrice' => (float)$targetPrice,
                ]
            ]
        ];
        $response = $this->httpClient->request('PUT', static::$apiUrl['updateInventoryPrice'], ['body' => json_encode($body)]);
        echo $targetPrice . "\n";
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        $data = $response->toArray();
        $combinedData = [
            'price' => $data,
            'batchRequestResult' => $this->getBatchRequestResult($data['batchId'])
        ];
        echo "Price set\n";
        $filename = "SETPRICE_{$stockCode}.json";
        $this->putToCache($filename, ['requeest'=>$body, 'response'=>$combinedData]);
    }

    public function createListing($data)
    {
        echo "-------------------------------API SENDING DATA CICEKESEPETI CONNECTOR-----------------------------------------------------------\n";
        $response = $this->httpClient->request('POST', static::$apiUrl['offers'], ['body' => $data]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
        }
        echo $response->getContent() . "\n";
        $responseData = $response->toArray();
        $combinedData = [
            'inventory' => $responseData,
            'batchRequestResult' => $this->getBatchRequestResult($responseData['batchId'])
        ];
        print_r($combinedData);
        $filename = "CREATE_LISTING_{$responseData['batchId']}.json";
        $this->putToCache($filename, ['request'=>$data, 'response'=>$combinedData]);
        sleep(5);
        return $responseData['batchId'];
    }

    public function updateProduct($data)
    {
        $response = $this->httpClient->request('PUT', static::$apiUrl['offers'], ['body' => $data]);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return;
        }
        $responseData = $response->toArray();
        sleep(5);
        $batchResult = $this->getBatchRequestResult($responseData['batchId']);
        $successCount = 0;
        $failCount = 0;
        foreach ($batchResult['items'] ?? [] as $item) {
            if ($item['status'] === 'Success') {
                $successCount++;
            } else {
                $failCount++;
            }
        }
        print_r($batchResult);
        echo "✅ Success Product Count: $successCount\n";
        echo "❌ Fail Product Count: $failCount\n";
        $combinedData = [
            'result' => $responseData,
            'batchRequestResult' => $batchResult
        ];
        echo "Product Updated \n";
        $filename = "UPDATE_LISTING_{$responseData['batchId']}.json";
        $this->putToCache($filename, ['request'=>$data, 'response'=>$combinedData]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getBatchRequestResult($batchId): array
    {
        $url = static::$apiUrl['batchStatus'] . $batchId;
        $response = $this->httpClient->request('GET', $url);
        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            echo "Error: $statusCode\n";
            return [];
        }
        sleep(5);
        return $response->toArray();
    }

}