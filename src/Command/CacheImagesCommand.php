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
            ->addOption('skip-bolcom', null, InputOption::VALUE_NONE, 'If set, Shopify objects will be skipped.')
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
        $pageSize = 150;
        $offset = 18000;

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
                $variantType = (empty($variantMarketplace)) ? 'Amazon' : $variantMarketplace->getMarketPlaceType();
                if (empty($variantType)) {
                    echo "Variant {$variant->getId()} has no marketplace->type.\n";
                    continue;
                }
                switch ($variantType) {
                    case 'Amazon':
                        if (!$input->getOption('skip-amazon')) self::processAmazon(variant: $variant);
                        break;
                    case 'Etsy':
                        if (!$input->getOption('skip-etsy')) self::processEtsy($variant);
                        break;
                    case 'Shopify':
                        if (!$input->getOption('skip-shopify')) self::processShopify($variant);
                        break;
                    case 'Trendyol':
                        if (!$input->getOption('skip-trendyol')) self::processTrendyol($variant);
                        break;
                    case 'Bol.com':
                        if (!$input->getOption('skip-bolcom')) self::processBolCom($variant);
                        break;
                    default:
                        break;
                }
            }
            $offset += $pageSize;
            echo "\nProcessed {$offset} of {$totalCount} ";
        }
        return Command::SUCCESS;
    }

    protected static function processTrendyol($variant)
    {
        $json = self::getApiResponse($variant->getId());
        $listingImageList = [];
        foreach ($json['images'] ?? [] as $image) {
            $listingImageList[] = static::processImage($image['url'], static::$trendyolFolder, "Trendyol_".str_replace(["https:", "/", ".", "_", "jpg"], '', $image['url']).".jpg");
        }
        $listingImageList = array_unique($listingImageList);
        $variant->fixImageCache($listingImageList);
    }

    protected static function processAmazon($variant)
    {
        $filename = PIMCORE_PROJECT_ROOT."/tmp/marketplaces/Amazon_ASIN_{$variant->getUniqueMarketplaceId()}.json";
        if (!file_exists($filename)) {
            return;
        }
        $json = json_decode(json: file_get_contents(filename: $filename) ?? [], associative: true) ?? [];
        $listingImageList = [];
        foreach ($json['images'][0]['images'] ?? [] as $image) {
            if ($image['height'] < 1000) {
                continue;
            }
            $listingImageList[] = static::processImage(url: $image['link'], parent: static::$amazonFolder, oldFileName: "Amazon_".str_replace(["https:", "/", ".", "_", "jpg"], '', $image['link']).".jpg");
        }
        $listingImageList = array_unique(array: $listingImageList);
        $variant->fixImageCache($listingImageList);
    }

    protected static function processShopify($variant)
    {
        $parentJson = self::getParentResponse($variant->getId());
        $imageArray = array_merge($parentJson['image'] ?? [], $parentJson['images'] ?? []);
        $listingImageList = [];
        $variantImage = null;
        foreach ($imageArray ?? [] as $image) {
            $id = $image["id"] ?? '';
            $src = $image['src'] ??'';
            $variant_ids = $parentJson['variant_ids'] ?? [];
            if (empty($id) || empty($src)) {
                continue;
            }
            $imgProcessed = static::processImage($src, static::$shopifyFolder, "Shopify_{$id}.jpg");
            $listingImageList[] = $imgProcessed;
            if (in_array($variant->getUniqueMarketplaceId(), $variant_ids)) {
                $variantImage = $imgProcessed;
            }
        }
        $variant->fixImageCache($listingImageList, $variantImage);
        echo "{$variant->getId()} ";
    }

    protected static function processEtsy($variant)
    {
        $json = self::getApiResponse($variant->getId());
        $parentJson = self::getParentResponse($variant->getId());
        $variantProperty = [];
        $listingImageList = [];
        foreach ($json['property_values'] ?? [] as $property) {
            foreach ($property['value_ids'] ?? [] as $valueId) {
                $variantProperty[] = "{$property['property_id']}_{$valueId}";
            }
        }

        $myVariantImage = null;
        foreach ($parentJson['variation_images'] ?? [] as $variationImage) {
            if (in_array("{$variationImage['property_id']}_{$variationImage['value_id']}", $variantProperty)) {
                $myVariantImage = $variationImage['image_id'];
                break;
            }
        }

        $variantImageObj = null;
        foreach ($parentJson["images"] ?? [] as $image) {
            $imgProcessed = static::processImage($image['url_fullxfull'] ?? '', static::$etsyFolder, "Etsy_{$image['listing_id']}_{$image['listing_image_id']}.jpg");
            $listingImageList[$image['listing_image_id']] = $imgProcessed;
            if ($myVariantImage === $image['listing_image_id']) {
                $variantImageObj = $imgProcessed;
            }
        }
        $variant->fixImageCache($listingImageList, $variantImageObj);
        echo "{$variant->getId()} ";        
    }

    protected static function processBolCom($variant)
    {
        $json = self::getApiResponse($variant->getId());
        $listingImageList = [];
        foreach ($json['assets']['assets'] ?? [] as $asset) {
            foreach ($asset['variants'] ?? [] as $assetVariant) {
                if (($assetVariant['size'] ?? '') === 'small') {
                    continue;
                }
                $img = static::processImage($assetVariant['url'], static::$bolcomFolder);
                $listingImageList[] = $img;
                if (($asset['usage'] ?? '') === 'PRIMARY') {
                    $variant->setImageUrl($img->getFullPath());
                }
            }
        }
        $variant->fixImageCache($listingImageList);
        echo "{$variant->getId()}: ";
    }

    protected static function processImage($url, $parent, $oldFileName = '')
    {
        if (empty($url)) {
            return null;
        }
        $dirty = false;
        $newFileName = self::createUniqueFileNameFromUrl($url);
        $asset = self::findImageByName($newFileName);
        if ($asset) {
            echo "_";
        } else {
            if ($oldFileName) {
                $asset = self::findImageByName($oldFileName);
                if ($asset) {
                    $asset->setFilename($newFileName);
                    $asset->setParent($parent);
                    $dirty = true;
                    echo 'R';
                }
            }
        } 
        if (!$asset) {
            try {
                $imageData = file_get_contents($url);
                echo "D";
            } catch (\Exception $e) {
                echo "Failed to get image data: " . $e->getMessage() . "\n";
                sleep(3);
                return null;
            }
            sleep(1);
            if ($imageData === false) {
                echo "-";
                return null;
            }
            $asset = new Asset\Image();
            $asset->setData($imageData);
            $asset->setFilename($newFileName);
            echo "+";
            $dirty = true;
        }

        $firstLetter = substr($newFileName, 0, 1);
        $secondLetter = substr($newFileName, 1, 1);
        $thirdLetter = substr($newFileName, 2, 1);

        $parent = Utility::checkSetAssetPath($firstLetter, $parent);
        $parent = Utility::checkSetAssetPath($secondLetter, $parent);
        $parent = Utility::checkSetAssetPath($thirdLetter, $parent);

        if ($asset->getParent() !== $parent) {
            $asset->setParent($parent);
            echo "P";
            $dirty = true;
        }
        
        try {
            if ($dirty) {
                $asset->save();
                echo ":";
            } else {
                echo ".";
            }
        } catch (\Exception $e) {
            echo "Failed to save asset: " . $e->getMessage() . "\n";
            return null;
        }
        return $asset;
    }
    
    protected static function findImageByName($imageName)
    {
        $assetList = new AssetListing();
        $assetList->setCondition("filename = ?", [$imageName]);
        $assetList->setLimit(1);
        return $assetList->current();
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
        return static::getResponseFromDb($id,'parentResponseJson');
    }

    public static function createUniqueFileNameFromUrl($url)
    {
        $pathInfo = pathinfo(parse_url($url, PHP_URL_PATH));        
        $hash = md5($url);
        $extension = strtolower($pathInfo['extension']);
        return "$hash.$extension";
    }

}