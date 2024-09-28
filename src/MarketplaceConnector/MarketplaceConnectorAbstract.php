<?php

namespace App\MarketplaceConnector;

use App\MarketplaceConnector\MarketplaceConnectorInterface;
use Pimcore\Model\DataObject\Marketplace;
use App\Command\CacheImagesCommand;
use Pimcore\Model\DataObject\Data\Link;


abstract class MarketplaceConnectorAbstract implements MarketplaceConnectorInterface
{
    public $marketplace = null;
    public $listings = [];

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
    }

    protected static function getCachedImage($url)
    {
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }
        $imageAsset = Utility::findImageByName(CacheImagesCommand::createUniqueFileNameFromUrl($url));
        if ($imageAsset) {
            return new \Pimcore\Model\DataObject\Data\ExternalImage(
                "https://mesa.iwa.web.tr/var/assets/".str_replace(" ", "%20", $imageAsset->getFullPath())
            );
        }
        return new \Pimcore\Model\DataObject\Data\ExternalImage($url);
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