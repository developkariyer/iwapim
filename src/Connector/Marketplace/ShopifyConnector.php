<?php

namespace App\Connector\Marketplace;

use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Pimcore\Model\DataObject\Data\ExternalImage;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject\Marketplace;
use App\Utils\Utility;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ShopifyConnector extends MarketplaceConnectorAbstract
{
    public static string $marketplaceType = 'Shopify';

    private string $graphqlUrl = PIMCORE_PROJECT_ROOT . '/src/GraphQL/Shopify/';

    private string $apiUrl;


    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function download($forceDownload = false)
    {
        echo "Getting from Shopify GraphQL\n";
        $allData = [];
        $productCursor = null;
        $totalCount = 0;
        $query = [
            'query' => file_get_contents($this->graphqlUrl . 'downloadListing.graphql'),
            'variables' => [
                'numProducts' => 50,
                'productCursor' => null,
                'numVariants' => 50,
                'variantCursor' => null,
                'numMedias' => 50,
                'mediaCursor' => null
            ]
        ];
        do {
            $query['variables']['cursor'] = $productCursor;
            $headersToApi = [
                'json' => $query,
                'headers' => [
                    'X-Shopify-Access-Token' => $this->marketplace->getAccessToken(),
                    'Content-Type' => 'application/json'
                ]
            ];
            try {
                $response = $this->httpClient->request('POST', $this->graphqlUrl . '/graphql.json', $headersToApi);
            } catch (ClientExceptionInterface $e) {
                echo $e->getMessage();
                break;
            }
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                echo $statusCode . "\n";
                break;
            }
            $newData = json_decode($response->getContent(), true);
            print_r(json_encode($newData));
            $itemsCount = count($newData['data']['products']['nodes'] ?? []);
            $totalCount += $itemsCount;
            echo "Count: $totalCount\n";

            /*$variantCursor = null;
            while ($variantHasNextPage) {
                $query['variables']['variantCursor'] = $variantCursor;
                $headersToApi['json'] = $query;
                try {
                    $response = $this->httpClient->request('POST', $this->graphqlUrl . '/graphql.json', $headersToApi);
                } catch (ClientExceptionInterface $e) {
                    echo $e->getMessage();
                }
                $newVari

            };*/

            break;
        } while ($productHasNextPage);
        return $allData;

        // TODO: Implement download() method.
    }

    public function downloadOrders()
    {
        // TODO: Implement downloadOrders() method.
    }

    public function downloadInventory()
    {
        // TODO: Implement downloadInventory() method.
    }

    public function import($updateFlag, $importFlag)
    {
        // TODO: Implement import() method.
    }

    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null)
    {
        // TODO: Implement setInventory() method.
    }

    public function setPrice(VariantProduct $listing, string $targetPrice, $targetCurrency = null, $sku = null, $country = null)
    {
        // TODO: Implement setPrice() method.
    }
}
