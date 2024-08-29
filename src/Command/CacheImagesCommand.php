<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\AmazonVariant;
use Pimcore\Model\DataObject\AmazonVariant\Listing as AmazonVariantListing;
use Pimcore\Model\DataObject\EtsyListing\Listing as EtsyListingListing;
use Pimcore\Model\DataObject\ShopifyListing\Listing as ShopifyListingListing;
use Pimcore\Model\DataObject\TrendyolVariant\Listing as TrendyolVariantListing;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Listing as AssetListing;
use Pimcore\Db;
use App\Utils\Utility;


#[AsCommand(
    name: 'app:cache-images',
    description: 'Cache images!'
)]

class CacheImagesCommand extends AbstractCommand
{
    
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
        if (!$input->getOption('skip-amazon')) {
            self::processAmazon();
        }
        if (!$input->getOption('skip-etsy')) {
            self::processEtsy();
        }
        if (!$input->getOption('skip-shopify')) {
            self::processShopify();
        }
        if (!$input->getOption('skip-trendyol')) {
            self::processTrendyol();
        }
        
        return Command::SUCCESS;
    }


    protected static function processTrendyol()
    {
        echo "Loading Trendyol objects...\n";
        $cacheFolder = Utility::checkSetAssetPath('Image Cache');
        $trendyolFolder = Utility::checkSetAssetPath('Trendyol', $cacheFolder);
        $listObject = new TrendyolVariantListing();
        $listObject->setUnpublished(true);
        $trendyolList = $listObject->load();
        foreach ($trendyolList as $trendyol) {
            echo "    Processing Trendyol object: {$trendyol->getId()}: ";
            $imageFolder = Utility::checkSetAssetPath("{$trendyol->getBarcode()}", $trendyolFolder);
            $listingImageList = [];
            $images = json_decode($trendyol->getImages() ?? [], true);
            foreach ($images as $image) {
                $imageName = "Trendyol_".str_replace(["https:", "/", ".", "_", "jpg"], '', $image['url']).".jpg";
                $asset = self::findImageByName($imageName);
                if ($asset) {
                    echo ".";
                } else {
                    try {
                        $imageData = file_get_contents($image['url']);
                    } catch (\Exception $e) {
                        echo "Failed to get image data: " . $e->getMessage() . "\n";
                        sleep(3);
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
            $trendyol->setImageGallery(new \Pimcore\Model\DataObject\Data\ImageGallery($items));
            $trendyol->save();
            echo "\n";
        }

    }

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

    }

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
    }    

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
    }

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


}
