<?php

namespace App\Model\DataObject;

use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\ExternalImage;
use Pimcore\Model\DataObject\Data\Hotspotimage;
use Pimcore\Model\DataObject\Data\ImageGallery;
use Pimcore\Model\DataObject\VariantProduct\Listing;
use App\Utils\Utility;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Asset;
use Carbon\Carbon;
use Pimcore\Model\Element\DuplicateFullPathException;
use Throwable;

class VariantProduct extends Concrete
{
    /**
     * Finds objects by a specific field. To be deleted, since PimCore has built-in findByField method.
     *
     * @param string $field The field name to query by.
     * @param mixed $value The value to query for.
     * @param int $limit The maximum number of results to return.
     * @param bool $unpublished Whether to include unpublished objects.
     * @return array An array of matching objects.
     */
    public static function findByField(string $field, mixed $value, int $limit = 1, bool $unpublished = false): array
    {
        $list = new Listing();
        $list->setCondition("`$field` = ? COLLATE utf8mb4_general_ci", [$value]);
        $list->setUnpublished($unpublished);
        $list->setLimit($limit);
        return $list->load() ?: [];
    }

    /**
     * Finds a single object by a specific field.
     *
     * @param string $field The field name to query by.
     * @param mixed $value The value to query for.
     * @return Concrete|null The matching object or null if not found.
     */
    public static function findOneByField(string $field, mixed $value, $object = null, $unpublished = false): ?Concrete
    {
        $list = \Pimcore\Model\DataObject\VariantProduct::findByField($field, $value, 1, $unpublished);
        if (!empty($list)) {
            foreach ($list as $item) {
                if (!$object instanceof \Pimcore\Model\DataObject\VariantProduct) {
                    return $item;
                }
                if ($object->getId() !== $item->getId()) {
                    return $item;
                }
            }
        }
        return null;
    }

    /**
     * @throws \Exception
     */
    public static function addUpdateVariant($variant, $importFlag, $updateFlag, $marketplace, $parent)
    {
        print_r($variant['apiResponseJson']);
        try {
            $object = \Pimcore\Model\DataObject\VariantProduct::findOneByField(
                'uniqueMarketplaceId',
                $variant['uniqueMarketplaceId'] ?? '',
                unpublished: true
            );
            if (!$object) {
                if (!$importFlag) {
                    return true;
                }
                $object = new \Pimcore\Model\DataObject\VariantProduct();
            }
            $result = $object->updateVariant($variant, $updateFlag, $marketplace, $parent);
            if ($result && empty($object->getMainProduct())) {
                if (!empty($variant['sku'])) {
                    $variant['sku'] = substr($variant['sku'], 0, 12);
                    if (!empty($variant['sku'])) {
                        $product = Product::getByIwasku($variant['sku'], 1);
                    }
                }
                if (empty($product) && !empty($variant['ean'])) {
                    $product = Product::getByEanGtin($variant['ean'], 1);
                }
                if (isset($product) && $product instanceof Product) {
                    echo "C";
                    $product->addVariant($object);
                    $product->save();
                }
            }
            return $object;
        } catch (\Exception $e) {
            echo "Error: {$e->getMessage()}\n";
            exit;
        }
    }

    /**
     * @throws Exception
     */
    public function jsonRead($fieldName)
    {
        $db = Db::get();
        return $db->fetchOne("SELECT json_data FROM iwa_json_store WHERE object_id = ? AND field_name = ?", [$this->getId(), $fieldName]);
    }

    /**
     * @throws Exception
     */
    public function jsonWrite($fieldName, $data): void
    {
        $db = Db::get();
        $db->executeStatement("INSERT INTO iwa_json_store (object_id, field_name, json_data) 
            VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json_data = ?", [$this->getId(), $fieldName, $data, $data]);
    }

    /**
     * @throws DuplicateFullPathException|Exception
     * @throws \Exception
     */
    public function updateVariant($variant, $updateFlag, $marketplace, $parent): VariantProduct|bool
    {
        if (!$updateFlag) {
            return true;
        }
        if (!$parent) {
            throw new \Exception('Parent is required for adding/updating VariantProduct');
        }
        $key_base = ($marketplace instanceof \Pimcore\Model\DataObject\Marketplace)
            ? "{$marketplace->getKey()} {$variant['title']} "
            : "Amazon {$variant['title']} ";
        $key_base.= Utility::sanitizeVariable($variant['attributes'] ?? '');
        $key_base = Utility::sanitizeVariable($key_base,250);
        $key = '';
        while (VariantProduct::findOneByField('key', "$key_base$key", $this, unpublished: true)) {
            $key = $key ? $key+1 : 1;
        }
        $this->setKey(trim("$key_base$key"));
        if (!empty($variant['imageUrl'])) {
            $this->setImageUrl($variant['imageUrl']);
        }
        $this->setUrlLink($variant['urlLink'] ?? null);
        $this->setSalePrice($variant['salePrice'] ?? '');
        $this->setSaleCurrency($variant['saleCurrency'] ?? '');
        $this->setTitle($variant['title'] ?? '');
        $this->setAttributes($variant['attributes'] ?? '');
        //$this->setEan($variant['ean'] ?? '');
        $this->setQuantity($variant['quantity'] ?? 0);
        $this->setUniqueMarketplaceId($variant['uniqueMarketplaceId'] ?? '');
        $this->setMarketplace($marketplace);
        $this->setMarketplaceType($marketplace->getMarketplaceType()); // might seem redundant but it's for caching purposes
        $this->setPublished($variant['published'] ?? false);
        $passiveFolder = Utility::checkSetPath("_Pasif", Utility::checkSetPath($marketplace->getKey(), Utility::checkSetPath("Pazaryerleri")));
        $publishedStatus = $variant['published'] ?? false;
        $this->setParent($publishedStatus ?  $parent : $passiveFolder);
        $this->setLastUpdate(Carbon::now());
        try {
            $result = $this->save();
        } catch (Throwable $e) {
            echo "Error: {$e->getMessage()}\n";
            return false;
        }
        echo "{$this->getId()} ";
        if (isset($variant['apiResponseJson'])) {
            $this->jsonWrite('apiResponseJson', $variant['apiResponseJson']);
        }
        if (isset($variant['parentResponseJson'])) {
            $this->jsonWrite('parentResponseJson', $variant['parentResponseJson']);
        }
        return $result;
    }

    /**
     * @throws \Exception
     */
    public function fixImageCache(array $listingImageList, $variantImage = null): void
    {
        if (empty($listingImageList)) {
            return;
        }
        $items = [];
        foreach ($listingImageList as $asset) {
            if (empty($asset)) {
                continue;
            }
            $advancedImage = new Hotspotimage();
            $advancedImage->setImage($asset);
            $items[] = $advancedImage;
        }
        $this->setImageGallery(new ImageGallery($items));
        $variantImage = $variantImage ?? reset($listingImageList);
        if ($variantImage instanceof Asset\Image) {
            $urlImage = new ExternalImage(
                "https://mesa.iwa.web.tr/var/assets/".str_replace(" ", "%20", $variantImage->getFullPath())
            );
            $this->setImageUrl($urlImage);
        }
        $this->save();
    }
}