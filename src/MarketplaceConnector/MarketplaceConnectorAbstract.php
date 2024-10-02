<?php

namespace App\MarketplaceConnector;

use App\MarketplaceConnector\MarketplaceConnectorInterface;
use Pimcore\Model\DataObject\Marketplace;
use App\Command\CacheImagesCommand;
use Pimcore\Model\DataObject\Data\Link;
use Symfony\Component\HttpClient\HttpClient;



abstract class MarketplaceConnectorAbstract implements MarketplaceConnectorInterface
{
    public $marketplace = null;
    public $listings = [];
    public $httpClient = null;

    public static $marketplaceType = '';

    public function __construct(Marketplace $marketplace)
    {
        if (!$marketplace instanceof Marketplace ||
            !$marketplace->getPublished() ||
            $marketplace->getMarketplaceType() !== static::$marketplaceType 
        ) {
            throw new \Exception("Marketplace is not published, is not ".static::$marketplaceType." or credentials are empty");
        }
        $this->marketplace = $marketplace;
        $this->httpClient = HttpClient::create();
    }

    protected function getUrlLink($url)
    {
        if (empty($url)) {
            return null;
        }
        $l = new Link();
        $l->setPath($url);
        return $l;
    }

}