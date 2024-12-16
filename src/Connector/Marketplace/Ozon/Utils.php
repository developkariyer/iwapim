<?php

namespace App\Connector\Marketplace\Ozon;

use App\Connector\Marketplace\Ozon\Connector as OzonConnector;
use App\Utils\Utility;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Utils
{
    public OzonConnector $connector;

    const string API_CATEGORY_TREE_URL = "https://api-seller.ozon.ru/v1/description-category/tree";

    public function __construct(OzonConnector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @throws TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface|DecodingExceptionInterface|ClientExceptionInterface
     */
    public function getCategoryTree(): array
    {
        $response = $this->connector->getApiResponse('POST', self::API_CATEGORY_TREE_URL, [], ['language' => 'EN']);
        Utility::setCustomCache('CATEGORY_TREE.json', PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/".urlencode($this->connector->getMarketplaceKey()), json_encode($response, JSON_PRETTY_PRINT));
        return $response;
    }
}