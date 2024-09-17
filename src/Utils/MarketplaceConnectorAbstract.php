<?php

namespace App\Utils;

use App\Utils\MarketplaceConnectorInterface;
use Pimcore\Model\DataObject\Marketplace;
use App\Command\CacheImagesCommand;


abstract class MarketplaceConnectorAbstract implements MarketplaceConnectorInterface
{
    public $marketplace = null;
    public $listings = [];

    public static $marketplaceType = '';

    public function __construct(Marketplace $marketplace)
    {
        if (!$marketplace instanceof Marketplace ||
            !$marketplace->getPublished() ||
            $marketplace->getMarketplaceType() !== static::$marketplaceType ||
            empty($marketplace->getAccessToken()) ||
            empty($marketplace->getApiUrl())
        ) {
            throw new \Exception("Marketplace is not published, is not Shopify or credentials are empty");
        }
        $this->marketplace = $marketplace;
        echo " initialiazed\n";
    }

    protected static function getCachedImage($url)
    {
        $imageAsset = Utility::findImageByName(CacheImagesCommand::createUniqueFileNameFromUrl($url));
        if ($imageAsset) {
            return new \Pimcore\Model\DataObject\Data\ExternalImage(
                "https://mesa.iwa.web.tr/var/assets/".str_replace(" ", "%20", $imageAsset->getFullPath())
            );
        }
        return new \Pimcore\Model\DataObject\Data\ExternalImage($url);
    }

}