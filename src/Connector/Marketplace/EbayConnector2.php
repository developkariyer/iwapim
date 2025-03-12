<?php

/*
 * This is a refactor of the EbayConnector class.
 */

namespace App\Connector\Marketplace;

use DOMDocument;
use Exception;
use InvalidArgumentException;
use Pimcore\Model\DataObject\VariantProduct;
use SimpleXMLElement;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class EbayConnector2 extends MarketplaceConnectorAbstract
{
    private static string $scopes = "https://api.ebay.com/oauth/api_scope
https://api.ebay.com/oauth/api_scope/sell.marketing.readonly
https://api.ebay.com/oauth/api_scope/sell.marketing
https://api.ebay.com/oauth/api_scope/sell.inventory.readonly
https://api.ebay.com/oauth/api_scope/sell.inventory
https://api.ebay.com/oauth/api_scope/sell.account.readonly
https://api.ebay.com/oauth/api_scope/sell.account
https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly
https://api.ebay.com/oauth/api_scope/sell.fulfillment
https://api.ebay.com/oauth/api_scope/sell.analytics.readonly
https://api.ebay.com/oauth/api_scope/sell.finances
https://api.ebay.com/oauth/api_scope/sell.payment.dispute
https://api.ebay.com/oauth/api_scope/commerce.identity.readonly
https://api.ebay.com/oauth/api_scope/sell.reputation
https://api.ebay.com/oauth/api_scope/sell.reputation.readonly
https://api.ebay.com/oauth/api_scope/commerce.notification.subscription
https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly
https://api.ebay.com/oauth/api_scope/sell.stores
https://api.ebay.com/oauth/api_scope/sell.stores.readonly
https://api.ebay.com/oauth/scope/sell.edelivery";


    const string API_CALL = 'API_CALL';
    const string XML_CALL = 'XML_CALL';
    const string REFRESH_TOKEN = 'REFRESH_TOKEN';
    const string ACCESS_TOKEN = 'ACCESS_TOKEN';


    private string $accessToken = '';
    private int $accessTokenExpiresAt = 0;

    public static string $marketplaceType = 'Ebay';


    /**
     * @param string $type
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function headers(string $type = self::API_CALL): array
    {
        return match ($type) {
            self::API_CALL => [
                'Authorization' => 'Bearer '.$this->getAccessToken(),
                'Content-Type' => 'application/json',
            ],
            self::XML_CALL => [
                'Content-Type' => 'text/xml; charset=utf-8',
            ],
            self::REFRESH_TOKEN, self::ACCESS_TOKEN => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . base64_encode("{$this->marketplace->getEbayClientId()}:{$this->marketplace->getEbayClientSecret()}"),
            ],
            default => [],
        };
    }

    /**
     * @throws Exception
     */
    private static function xmlToArray(string $xml): array
    {
        $xmlObject = new SimpleXMLElement($xml, LIBXML_NOCDATA);
        $json = json_encode($xmlObject);
        return json_decode($json, true);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     */
    private function apiCall(string $method, string $url, array $data = [], string $type = self::API_CALL): array
    {
        if (!in_array($type, [self::REFRESH_TOKEN, self::ACCESS_TOKEN, self::API_CALL, self::XML_CALL])) {
            throw new InvalidArgumentException('Invalid type');
        }
        if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'])) {
            throw new InvalidArgumentException('Invalid method');
        }
        $data['headers'] = array_merge($data['headers'] ?? [], $this->headers($type));
        try {
            $response = $this->httpClient->request($method, $url, $data);
            if ($response->getStatusCode() != 200) {
                echo "API call failed with {$response->getStatusCode()}\n";
                throw new Exception("API call failed with {$response->getStatusCode()}: {$response->getContent(false)}");
            }
            return ($type === self::XML_CALL) ? static::xmlToArray($response->getContent(false)) : $response->toArray();
        } catch (Exception $e) {
            if (isset($response)) {
                echo "Response Status Code: " . $response->getStatusCode() . "\n";
                echo "Response Headers:\n" . print_r($response->getHeaders(false), true) . "\n";
                echo "Raw Response Body:\n" . $response->getContent(false) . "\n";
            }
            throw new Exception('API call failed with exception: ' . $e->getMessage());
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    private function getAccessToken(): string
    {
        if ($this->accessToken && $this->accessTokenExpiresAt > time()) {
            return $this->accessToken;
        }
        echo "API CALL: getAccessToken\n";
        $url = "https://api.ebay.com/identity/v1/oauth2/token";
        $method = 'POST';
        $data = [
            'body' => http_build_query([
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->marketplace->getEbayRefreshToken(),
                'scope' => str_replace("\n", " ", self::$scopes)
            ]),
        ];
        try {   
            $response =$this->apiCall($method, $url, $data, self::ACCESS_TOKEN);
        } catch (Exception $e) {
            echo "API CALL: getAccessToken failed: ". $e->getMessage() . "\n";
            //$this->getRefreshToken();
            throw new Exception('New refresh token is requested. Save it and re-run the script.');
        }
        $this->accessToken = $response['access_token'] ?? '';
        $this->accessTokenExpiresAt = time() + $response['expires_in'] ?? 0;
        return $this->accessToken;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function getRefreshToken(): void
    {
        echo "API CALL: getRefreshToken\n";
        $url = "https://api.ebay.com/identity/v1/oauth2/token";
        $method = 'POST';
        $data = [
            'body' => http_build_query([
                'grant_type' => 'authorization_code',
                'code' => urldecode($this->marketplace->getEbayAuthCode()),
                'redirect_uri' => $this->marketplace->getEbayRuName(),
            ]),
        ];
        $response =$this->apiCall($method, $url, $data, self::REFRESH_TOKEN);
        echo "Refresh token: " . $response['refresh_token'] . "\n";
        // TODO: save refresh token
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getInventoryItems(): array
    {
        echo "API CALL: getInventoryItems\n";
        $url = "https://api.ebay.com/sell/inventory/v1/inventory_item";
        $method = 'GET';
        $data = [
            'query' => [
                'limit' => 5,
                'offset' => 0
            ]
        ];
        return $this->apiCall($method, $url, $data);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \DOMException
     */
    public function getSellerList(): array
    {
        echo "API CALL: getSellerList\n";
        $url = "https://api.ebay.com/ws/api.dll";
        $method = 'POST';

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $root = $dom->createElement('GetSellerListRequest');
        $root->setAttribute('xmlns', 'urn:ebay:apis:eBLBaseComponents');
        $requesterCredentials = $dom->createElement('RequesterCredentials');
        $authToken = $dom->createElement('eBayAuthToken', $this->getAccessToken());
        $requesterCredentials->appendChild($authToken);
        $root->appendChild($requesterCredentials);
        $granularity = $dom->createElement('GranularityLevel', 'Fine');
        $root->appendChild($granularity);
        $pagination = $dom->createElement('Pagination');
        $entriesPerPage = $dom->createElement('EntriesPerPage', '200');
        $pageNumber = $dom->createElement('PageNumber', '1');
        $pagination->appendChild($entriesPerPage);
        $pagination->appendChild($pageNumber);
        $root->appendChild($pagination);
        $now = gmdate('Y-m-d\TH:i:s\Z'); // Current time in UTC
        $endTime = gmdate('Y-m-d\TH:i:s\Z', strtotime('+119 days'));
        $root->appendChild($dom->createElement('EndTimeFrom', $now));
        $root->appendChild($dom->createElement('EndTimeTo', $endTime));
        $root->appendChild($dom->createElement('IncludeVariations', 'true'));
        $dom->appendChild($root);
        $xmlBody = $dom->saveXML();

        $data['headers'] = [
            'X-EBAY-API-COMPATIBILITY-LEVEL' => '1349',
            'X-EBAY-API-CALL-NAME' => 'GetSellerList',
            'X-EBAY-API-SITEID' => '0',
        ];
        $data['body'] = $xmlBody;
        return $this->apiCall($method, $url, $data, self::XML_CALL);
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

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
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