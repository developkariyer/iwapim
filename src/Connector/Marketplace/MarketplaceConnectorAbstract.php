<?php

namespace App\Connector\Marketplace;

use App\Connector\Marketplace\MarketplaceConnectorInterface;
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

    protected function getLatestOrderUpdate()
    {
        $db = \Pimcore\Db::get();
        return $db->fetchOne(
            "SELECT COALESCE(MAX(json_extract(json, '$.updated_at')), '2000-01-01T00:00:00Z') FROM iwa_marketplace_orders WHERE marketplace_id = ?",
            [$this->marketplace->getId()]
        );
    }

    protected function getMarketplace()
    {
        return $this->marketplace;
    }

}