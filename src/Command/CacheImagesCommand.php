<?php

namespace App\Command;

use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\Asset\Folder;
use Pimcore\Model\Element\DuplicateFullPathException;
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

    static ?Folder $cacheFolder;
    static ?Folder $amazonFolder;
    static ?Folder $etsyFolder;
    static ?Folder $shopifyFolder;
    static ?Folder $trendyolFolder;
    static ?Folder $bolcomFolder;
    static ?Folder $hepsiburadaFolder;

    protected function configure(): void
    {
        $this
            ->addOption('amazon', null, InputOption::VALUE_NONE, 'If set, Amazon objects will be processed.')
            ->addOption('etsy', null, InputOption::VALUE_NONE, 'If set, Etsy objects will be processed.')
            ->addOption('shopify', null, InputOption::VALUE_NONE, 'If set, Shopify objects will be processed.')
            ->addOption('bolcom', null, InputOption::VALUE_NONE, 'If set, Shopify objects will be processed.')
            ->addOption('trendyol', null, InputOption::VALUE_NONE, 'If set, Trendyol objects will be processed.')
            ->addOption('hepsiburada', null, InputOption::VALUE_NONE, 'If set, Hepsiburada objects will be processed.')
            ->addOption('all', null, InputOption::VALUE_NONE, 'If set, all objects will be processed.');
    }

    /**
     * @throws DuplicateFullPathException|Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        static::$cacheFolder = Utility::checkSetAssetPath('Image Cache');
        static::$amazonFolder = Utility::checkSetAssetPath('Amazon', static::$cacheFolder);
        static::$etsyFolder = Utility::checkSetAssetPath('Etsy', static::$cacheFolder);
        static::$shopifyFolder = Utility::checkSetAssetPath('Shopify', static::$cacheFolder);
        static::$trendyolFolder = Utility::checkSetAssetPath('Trendyol', static::$cacheFolder);
        static::$bolcomFolder = Utility::checkSetAssetPath('Bol.com', static::$cacheFolder);
        static::$hepsiburadaFolder = Utility::checkSetAssetPath('Hepsiburada', static::$cacheFolder);

        $listingObject = new VariantProduct\Listing();
        $listingObject->setUnpublished(false);
        $listingObject->setOrderKey('id');
        $listingObject->setOrder('DESC');
        $pageSize = 150;
        $offset = 0;
        $index = 0;
        while (true) {
            $listingObject->setLimit($pageSize);
            $listingObject->setOffset($offset);
            $variants = $listingObject->load();
            if (empty($variants)) {
                break;
            }
            foreach ($variants as $variant) {
                $index++;
                echo "\rProcessing {$index} ...";
                    $variantMarketplace = $variant->getMarketplace();
                $variantType = (empty($variantMarketplace)) ? 'Amazon' : $variantMarketplace->getMarketPlaceType();
                if (empty($variantType)) {
                    echo "Variant {$variant->getId()} has no marketplace->type.\n";
                    continue;
                }
                if (in_array($variantType, ['Amazon', 'Etsy', 'Shopify', 'Trendyol', 'Bol.com', 'Hepsiburada'])) {
                    echo " $variantType ";
                } else {
                    continue;
                }
                switch ($variantType) {
                    case 'Amazon':
                        if ($input->getOption('amazon') || $input->getOption('all')) self::processAmazon(variant: $variant);
                        break;
                    case 'Etsy':
                        if ($input->getOption('etsy') || $input->getOption('all')) self::processEtsy($variant);
                        break;
                    case 'Shopify':
                        if ($input->getOption('shopify') || $input->getOption('all')) self::processShopify($variant);
                        break;
                    case 'Trendyol':
                        if ($input->getOption('trendyol') || $input->getOption('all')) self::processTrendyol($variant);
                        break;
                    case 'Bol.com':
                        if ($input->getOption('bolcom') || $input->getOption('all')) self::processBolCom($variant);
                        break;
                    case 'Hepsiburada':
                        if ($input->getOption('hepsiburada') || $input->getOption('all')) self::processHepsiburada($variant);
                        break;
                    default:
                        break;
                }
                echo "\n";
            }
            $offset += $pageSize;
        }
        return Command::SUCCESS;
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    protected static function processTrendyol(VariantProduct $variant): void
    {
        $json = json_decode($variant->jsonRead('apiResponseJson'), true);
        $listingImageList = [];
        foreach ($json['images'] ?? [] as $image) {
            $listingImageList[] = static::processImage($image['url'], static::$trendyolFolder, "Trendyol_".str_replace(["https:", "/", ".", "_", "jpg"], '', $image['url']).".jpg");
        }
        $listingImageList = array_unique($listingImageList);
        $variant->fixImageCache($listingImageList);
        echo "{$variant->getId()} ";
    }

    protected static function processHepsiburada(VariantProduct $variant): void
    {
        $json = json_decode($variant->jsonRead('apiResponseJson'), true);
        $listingImageList = [];
        foreach ($json['attributes']['images'] ?? [] as $image) {
            $image = preg_replace('#(https?://)/*#', '$1', $image);
            $image = preg_replace('#([^:])//+#', '$1/', $image);
            $image = str_replace(' ', '', $image);
            $listingImageList[] = static::processImage($image, static::$hepsiburadaFolder,"Hepsiburada_" . basename($image));
        }
        $listingImageList = array_unique($listingImageList);
        $variant->fixImageCache($listingImageList);
        echo "{$variant->getId()} ";
    }

    /**
     * @throws Exception|DuplicateFullPathException
     */
    protected static function processAmazon($variant): void
    {
        $json = Utility::retrieveJsonData($variant->getUniqueMarketplaceId());
        if (empty($json)) {
            echo "NULL for {$variant->getId()}.\n";
            return;
        }
        $listingImageList = [];
        foreach ($json['images'][0]['images'] ?? [] as $image) {
            if ($image['height'] < 1000) {
                continue;
            }
            $listingImageList[] = static::processImage(url: $image['link'], parent: static::$amazonFolder, oldFileName: "Amazon_".str_replace(["https:", "/", ".", "_", "jpg"], '', $image['link']).".jpg");
        }
        $listingImageList = array_unique(array: $listingImageList);
        $variant->fixImageCache($listingImageList);
        echo "{$variant->getId()} ";
    }

    /**
     * @throws DuplicateFullPathException
     */
    protected static function processShopify($variant): void
    {
        $parentJson = json_decode($variant->jsonRead('parentResponseJson'), true);
        //$imageArray = array_merge($parentJson['image'] ?? [], $parentJson['images'] ?? []);
        $imageArray = $parentJson['media']['nodes'] ?? [];
        $listingImageList = [];
        $variantImage = null;
        foreach ($imageArray as $image) {
            $id = basename($image["id"]) ?? '';
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

    /**
     * @throws DuplicateFullPathException
     */
    protected static function processEtsy($variant): void
    {
        $json = json_decode($variant->jsonRead('apiResponseJson'), true);
        $parentJson = json_decode($variant->jsonRead('parentResponseJson'), true);
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
            $imgProcessed = static::processImage($image['url_fullxfull'] ?? '', static::$etsyFolder);
            $listingImageList[] = $imgProcessed;
            if ($myVariantImage === $image['listing_image_id']) {
                $variantImageObj = $imgProcessed;
            }
        }
        $listingImageList = array_unique($listingImageList);
        $variant->fixImageCache($listingImageList, $variantImageObj);
        echo "{$variant->getId()} ";        
    }

    /**
     * @throws DuplicateFullPathException
     */
    protected static function processBolCom($variant): void
    {
        $json = json_decode($variant->jsonRead('apiResponseJson'), true);
        $listingImageList = [];
        foreach ($json['assets']['assets'] ?? [] as $asset) {
            foreach ($asset['variants'] ?? [] as $assetVariant) {
                if (($assetVariant['size'] ?? '') === 'small') {
                    continue;
                }
                $img = static::processImage($assetVariant['url'], static::$bolcomFolder);
                $listingImageList[] = $img;
                if (($asset['usage'] ?? '') === 'PRIMARY') {
                    $variant->setImageUrl(Utility::getCachedImage($img->getFullPath()));
                }
            }
        }
        $variant->fixImageCache($listingImageList);
        echo "{$variant->getId()}: ";
    }

    /**
     * @throws DuplicateFullPathException
     */
    protected static function processImage($url, $parent, $oldFileName = ''): false|Asset|Asset\Image|null
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
                    echo 'R';
                }
            }
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

    protected static function findImageByName($imageName): false|Asset
    {
        $assetList = new AssetListing();
        $assetList->setCondition("filename = ?", [$imageName]);
        $assetList->setLimit(1);
        return $assetList->current();
    }

    public static function createUniqueFileNameFromUrl($url): string
    {
        $pathInfo = pathinfo(parse_url($url, PHP_URL_PATH));        
        $hash = md5($url);
        $extension = strtolower($pathInfo['extension']);
        return "$hash.$extension";
    }

}