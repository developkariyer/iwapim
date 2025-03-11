<?php

/*
 * This is a refactor of the EbayConnector class.
 */

namespace App\Connector\Marketplace;

use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;

class EbayConnector2 extends MarketplaceConnectorAbstract
{

    private string $accessToken = '';
    private int $accessTokenExpiresAt = 0;

    public static string $marketplaceType = 'Ebay';


    private function headers($type = 'API_CALL'): array
    {
        if ($type === 'API_CALL' && (!$this->accessToken || $this->accessTokenExpiresAt < time())) {
            $this->getAccessToken();
        }
        return match ($type) {
            'API_CALL' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ],
            'REFRESH_TOKEN', 'ACCESS_TOKEN' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . base64_encode("{$this->marketplace->getEbayClientId()}:{$this->marketplace->getEbayClientSecret()}"),
            ],
            default => [],
        };
    }

    private function apiCall(string $method, string $url, array $body = [], string $type = 'API_CALL')
    {
        if (!in_array($type, ['REFRESH_TOKEN', 'ACCESS_TOKEN', 'API_CALL'])) {
            throw new \InvalidArgumentException('Invalid type');
        }
        if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'])) {
            throw new \InvalidArgumentException('Invalid method');
        }
        try {
            $response = $this->httpClient->request(
                $method,
                $url,
                [
                    'body' => $body,
                    'headers' => $this->headers($type),
                ]
            );
            if ($response->getStatusCode() != 200) {
                throw new \Exception('API call failed: ' . $response->getContent());
            }
            return $response->toArray();
        } catch (\Exception $e) {
            throw new \Exception('API call failed: ' . $e->getMessage());
        }
    }

    private function getAccessToken(): void
    {
        $url = "https://api.ebay.com/identity/v1/oauth2/token";
        $method = 'POST';
        $body = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->marketplace->getEbayRefreshToken(),
            'scope' => "https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/sell.reputation https://api.ebay.com/oauth/api_scope/sell.reputation.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly https://api.ebay.com/oauth/api_scope/sell.stores https://api.ebay.com/oauth/api_scope/sell.stores.readonly https://api.ebay.com/oauth/scope/sell.edelivery"
        ];
        try {   
            $response =$this->apiCall($method, $url, $body, 'ACCESS_TOKEN');
        } catch (\Exception $e) {
            $this->getRefreshToken();
            throw new \Exception('New refresh token is requested. Save it and re-run the script.');
        }
        $this->accessToken = $response['access_token'] ?? '';
        $this->accessTokenExpiresAt = time() + $response['expires_in'] ?? 0;
    }

    private function getRefreshToken(): void
    {
        $url = "https://api.ebay.com/identity/v1/oauth2/token";
        $method = 'POST';
        $body = [
            'grant_type' => 'authorization_code',
            'code' => urldecode($this->marketplace->getEbayAuthCode()),
            'redirect_uri' => $this->marketplace->getEbayRuName(),
        ];
        $response =$this->apiCall($method, $url, $body, 'REFRESH_TOKEN');
        echo "Refresh token: " . $response['refresh_token'] . "\n";
        // TODO: save refresh token
    }

    public function getInventoryItems(): array
    {
        $url = "https://api.ebay.com/sell/inventory/v1/inventory_item";
        $method = 'GET';
        $data = [
            'query' => [
                'limit' => 5,
                'offset' => 0
            ]
        ];
        $response = $this->apiCall($method, $url, $data);
        return $response ?? [];
    }

    public function download(bool $forceDownload = false): void
    {
    }

    public function downloadInventory(): void 
    {
    }

    public function downloadOrders(): void
    {
    }

    public function import($updateFlag, $importFlag): void
    {
    }
 
    public function setInventory(VariantProduct $listing, int $targetValue, $sku = null, $country = null): void
    {
    }

    public function setPrice(VariantProduct $listing, string $targetPrice, $targetCurrency = null, $sku = null, $country = null): void
    {
    }

    public function searchProduct(string $searchText, int $page = 1, int $limit = 10): array
    {
        $method = 'GET';
        $url = "https://api.ebay.com/buy/browse/v1/item_summary/search";
        $data = [
            'query' => [
                'q' => $searchText,
                'limit' => $limit,
                'offset' => ($page - 1) * $limit
            ]
        ];
        $response = $this->apiCall($method, $url, $data);
        return $response['itemSummaries'] ?? [];
    }
}