<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Listing as AssetListing;
use App\Utils\Utility;


#[AsCommand(
    name: 'app:cache-images',
    description: 'Cache images!'
)]

class CacheImagesCommand extends AbstractCommand
{
    
    static $cacheFolder;
    static $amazonFolder;
    static $etsyFolder;
    static $shopifyFolder;
    static $trendyolFolder;
    static $bolcomFolder;

    protected function configure()
    {
        $this
//            ->addArgument('marketplace', InputOption::VALUE_OPTIONAL, 'The marketplace to import from.')
            ->addOption('skip-amazon', null, InputOption::VALUE_NONE, 'If set, Amazon objects will be skipped.')
            ->addOption('skip-etsy', null, InputOption::VALUE_NONE, 'If set, Etsy objects will be skipped.')
            ->addOption('skip-shopify', null, InputOption::VALUE_NONE, 'If set, Shopify objects will be skipped.')
            ->addOption('skip-trendyol', null, InputOption::VALUE_NONE, 'If set, Trendyol objects will be skipped.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        static::$cacheFolder = Utility::checkSetAssetPath('Image Cache');
        static::$amazonFolder = Utility::checkSetAssetPath('Amazon', static::$cacheFolder);
        static::$etsyFolder = Utility::checkSetAssetPath('Etsy', static::$cacheFolder);
        static::$shopifyFolder = Utility::checkSetAssetPath('Shopify', static::$cacheFolder);
        static::$trendyolFolder = Utility::checkSetAssetPath('Trendyol', static::$cacheFolder);
        static::$bolcomFolder = Utility::checkSetAssetPath('Bol.com', static::$cacheFolder);

        $listingObject = new VariantProduct\Listing();
        $listingObject->setUnpublished(true);
        $pageSize = 50;
        $offset = 16800;

        while (true) {
            $listingObject->setLimit($pageSize);
            $listingObject->setOffset($offset);
            $variants = $listingObject->load();
            if (empty($variants)) {
                break;
            }
            $totalCount = $listingObject->getTotalCount();
            foreach ($variants as $variant) {
                $variantMarketplace = $variant->getMarketplace();
                if (empty($variantMarketplace)) {
                    echo "Variant {$variant->getId()} has no marketplace.\n";
                    continue;
                }
                $variantType = $variantMarketplace->getMarketPlaceType();
                if (empty($variantType)) {
                    echo "Variant {$variant->getId()} has no marketplace->type.\n";
                    continue;
                }
                switch ($variantType) {
                    case 'Amazon':
//                        self::processAmazon($variant);
                        break;
                    case 'Etsy':
//                        self::processEtsy($variant);
                        break;
                    case 'Shopify':
//                        self::processShopify($variant);
                        break;
                    case 'Trendyol':
                        self::processTrendyol($variant);
                        break;
                    case 'Bol.com':
//                        self::processBolCom($variant);
                        break;
                    default:
                        break;
                }
                echo "{$variant->getId()}";
            }
            echo "\nProcessed {$offset} of {$totalCount}";
            $offset += $pageSize;
        }
        return Command::SUCCESS;
    }

    protected static function processTrendyol($variant)
    {
        $json = self::getApiResponse($variant->getId());
        $listingImageList = [];
        foreach ($json['images'] as $image) {
            $listingImageList[] = static::processImage($image['url'], static::$trendyolFolder, "Trendyol_".str_replace(["https:", "/", ".", "_", "jpg"], '', $image['url']).".jpg");
        }
        $listingImageList = array_unique($listingImageList);
        $variant->fixImageCache($listingImageList);
    }

    protected static function processShopify($variant)
    {
        $json = self::getApiResponse($variant->getId());
        $parentJson = self::getParentResponse($variant->getId());
        $listingImageList = [];
        $variantImage = null;
        foreach ($parentJson['images'] as $image) {
            $imgProcessed = static::processImage($image['src'], static::$shopifyFolder, "Shopify_{$image['id']}.jpg");
            $listingImageList[] = $imgProcessed;
            if (in_array($variant->getUniqueMarketplaceId(), $parentJson['variant_ids'])) {
                $variantImage = $imgProcessed;
            }
        }
        $variant->fixImageCache($listingImageList, $variantImage);
    }

    protected static function processImage($url, $parent, $oldFileName = '')
    {
        $newFileName = self::createUniqueFileNameFromUrl($url);
        if ($oldFileName) {
            $asset = self::findImageByName($oldFileName);
            if ($asset) {
                $asset->setFilename($newFileName);
                $asset->setParent($parent);
                $asset->save();
                echo 'R';
            }
        }
        if (empty($asset)) {
            $asset = self::findImageByName($newFileName);
            echo '.';
        }
        if (!$asset) {
            try {
                $imageData = file_get_contents($url);
            } catch (\Exception $e) {
                echo "Failed to get image data: " . $e->getMessage() . "\n";
                sleep(3);
                return null;
            }
            sleep(2);
            if ($imageData === false) {
                echo "-";
                return null;
            }
            $asset = new Asset\Image();
            $asset->setData($imageData);
            $asset->setFilename($newFileName);
            $asset->setParent($parent);
            try {
                $asset->save();
                echo "+";
            } catch (\Exception $e) {
                echo "Failed to save asset: " . $e->getMessage() . "\n";
                return null;
            }
        }
        return $asset;
    }
    
/*
    protected static function processShopify()
    {
        echo "Loading Shopify objects...\n";
        $cacheFolder = Utility::checkSetAssetPath('Image Cache');
        $shopifyFolder = Utility::checkSetAssetPath('Shopify', $cacheFolder);
        $listObject = new ShopifyListingListing();
        $listObject->setUnpublished(true);
        $shopifyList = $listObject->load();
        foreach ($shopifyList as $shopify) {
            echo "    Processing Shopify object: {$shopify->getId()}: ";
            $imageFolder = Utility::checkSetAssetPath("{$shopify->getShopifyId()}", $shopifyFolder);
            $listingImageList = [];
            $images = json_decode($shopify->getImagesjson() ?? [], true);
            foreach ($images as $image) {
                $imageName = "Shopify_{$image['id']}.jpg";
                $asset = self::findImageByName($imageName);
                if ($asset) {
                    echo ".";
                } else {
                    sleep(1);
                    try {
                        $imageData = file_get_contents($image['src']);
                    } catch (\Exception $e) {
                        echo "Failed to get image data: " . $e->getMessage() . "\n";
                        sleep(3);
                        continue;
                    }
                    if ($imageData === false) {
                        echo "-";
                        continue;
                    }
                    $asset = new Asset\Image();
                    $asset->setData($imageData);
                    $asset->setFilename($imageName);
                    $asset->setParent($imageFolder);
                    try {
                        $asset->save();
                        echo "+";
                    } catch (\Exception $e) {
                        echo "Failed to save asset: " . $e->getMessage() . "\n";
                        continue;
                    }
                } 
                $listingImageList[] = $asset;
            }
            $items = [];
            foreach($listingImageList as $img){
                $advancedImage = new \Pimcore\Model\DataObject\Data\Hotspotimage();
                $advancedImage->setImage($img);
                $items[] = $advancedImage;
            }
            $shopify->setImageGallery(new \Pimcore\Model\DataObject\Data\ImageGallery($items));
            $shopify->save();        
            echo "\n";
        }

    }*/
/*
    protected static function processEtsy()
    {
        echo "Loading Etsy objects...\n";
        $cacheFolder = Utility::checkSetAssetPath('Image Cache');
        $etsyFolder = Utility::checkSetAssetPath('Etsy', $cacheFolder);
        $listObject = new EtsyListingListing();
        $listObject->setUnpublished(true);
        $etsyList = $listObject->load();
        foreach ($etsyList as $etsy) {
            echo "    Processing Etsy object: {$etsy->getId()}: ";
            $imageFolder = Utility::checkSetAssetPath("{$etsy->getListingId()}", $etsyFolder);
            $listingImageList = [];
            $images = json_decode($etsy->getImages() ?? [], true);
            foreach ($images as $image) {
                $imageName = "Etsy_{$image['listing_id']}_{$image['listing_image_id']}.jpg";
                $asset = self::findImageByName($imageName);
                if ($asset) {
                    echo ".";
                } else {
                    try {
                        $imageData = file_get_contents($image['url_fullxfull']);
                    } catch (\Exception $e) {
                        echo "Failed to get image data: " . $e->getMessage() . "\n";
                        sleep(2);
                        continue;
                    }
                    sleep(2);
                    if ($imageData === false) {
                        echo "-";
                        continue;
                    }
                    $asset = new Asset\Image();
                    $asset->setData($imageData);
                    $asset->setFilename($imageName);
                    $asset->setParent($imageFolder);
                    try {
                        $asset->save();
                        echo "+";
                    } catch (\Exception $e) {
                        echo "Failed to save asset: " . $e->getMessage() . "\n";
                        continue;
                    }
                }
                $listingImageList[] = $asset;
            }
            $items = [];
            foreach($listingImageList as $img){
                $advancedImage = new \Pimcore\Model\DataObject\Data\Hotspotimage();
                $advancedImage->setImage($img);
                $items[] = $advancedImage;
            }
            $etsy->setImageGallery(new \Pimcore\Model\DataObject\Data\ImageGallery($items));
            $etsy->save();        
            echo "\n";
        }
    }    */
/*
    protected static function processAmazon()
    {
        echo "Loading Amazon objects...";
        $cacheFolder = Utility::checkSetAssetPath('Image Cache');
        $amazonFolder = Utility::checkSetAssetPath('Amazon', $cacheFolder);
        $listObject = new AmazonVariantListing();
        $listObject->setUnpublished(true);
        $amazonList = $listObject->load();
        echo "\n";
        foreach ($amazonList as $amazon) {
            echo "    Processing Amazon object: {$amazon->getId()}: ";
            $summaries = json_decode($amazon->getSummaries() ?? [['asin'=>'UNKNOWN']], true);
            $attributes = json_decode($amazon->getAttributes() ?? [], true);
            $asin = $summaries[0]['asin'];
            $imageFolder = Utility::checkSetAssetPath($asin, $amazonFolder);
            $listingImageList = [];
            foreach ($attributes as $key=>$value) {
                if (strpos($key, 'image') !== false) {
                    foreach ($value as $potentialImage) {
                        if (isset($potentialImage['media_location'])) {
                            $imageName = "Amazon_".str_replace(["https:", "+", "/", ".", "_", "jpg"], '', $potentialImage['media_location']).".jpg";
                            $asset = self::findImageByName($imageName);
                            if ($asset) {
                                echo ".";
                            } else {
                                try {
                                    $imageData = file_get_contents($potentialImage['media_location']);
                                } catch (\Exception $e) {
                                    echo "Failed to get image data: " . $e->getMessage() . "\n";
                                    sleep(2);
                                    continue;
                                }
                                sleep(1);
                                if ($imageData === false) {
                                    echo "-";
                                    continue;
                                }
                                $asset = new Asset\Image();
                                $asset->setData($imageData);
                                $asset->setFilename($imageName);
                                $asset->setParent($imageFolder);
                                try {
                                    $asset->save();
                                    echo "+";
                                } catch (\Exception $e) {
                                    echo "Failed to save asset: " . $e->getMessage() . "\n";
                                    continue;
                                }
                            }
                            $listingImageList[] = $asset;
                        }
                    }
                }
            }
            if (empty($listingImageList)) {
                echo "No images found.\n";
            } else {
                $items = [];
                foreach($listingImageList as $img){
                    $advancedImage = new \Pimcore\Model\DataObject\Data\Hotspotimage();
                    $advancedImage->setImage($img);
                    $items[] = $advancedImage;
                }
                $amazon->setImageGallery(new \Pimcore\Model\DataObject\Data\ImageGallery($items));
                $amazon->save();
                echo "\n";
            }
        }
    }*/

    protected static function findImageByName($imageName)
    {
        $assetList = new AssetListing();
        $assetList->setCondition("filename = ?", [$imageName]);
        $assetList->setLimit(1);
        return $assetList->current();
    }

    protected static function findImageByProperty($imageProperty, $propertyName): ?Asset
    {
        $db = Db::get();
        if ($result = $db->fetchOne("SELECT cpath FROM properties WHERE `ctype` = 'asset' AND `name` LIKE ? AND `data` LIKE ? LIMIT 1", [$imageProperty, $propertyName])) {
            return Asset::getByPath($result);
        }
        return null;
    }

    private static function getResponseFromDb($id, $fieldName)
    {
        $db = \Pimcore\Db::get();
        $response = $db->fetchOne('SELECT json_data FROM iwa_json_store WHERE object_id=? AND field_name=? LIMIT 1', [$id, $fieldName]);
        if (empty($response)) {
            return [];
        }
        return json_decode($response, true);
    }

    private static function getApiResponse($id)
    {
        return static::getResponseFromDb($id, 'apiResponseJson');
    }

    private static function getParentResponse($id)
    {
        return static::getResponseFromDb($id,'parentApiResponseJson');
    }

    private static function createUniqueFileNameFromUrl($url)
    {
        $pathInfo = pathinfo(parse_url($url, PHP_URL_PATH));        
        $hash = md5($url);
        $extension = strtolower($pathInfo['extension']);
        return "$hash.$extension";
    }

}
