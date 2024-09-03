<?php

namespace App\Model\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\VariantProduct\Listing;
use App\Utils\Utility;
use Pimcore\Model\DataObject\Product;

class VariantProduct extends Concrete
{
    /**
     * Finds objects by a specific field.
     *
     * @param string $field The field name to query by.
     * @param mixed $value The value to query for.
     * @param int $limit The maximum number of results to return.
     * @param bool $unpublished Whether to include unpublished objects.
     * @return array An array of matching objects.
     */
    public static function findByField($field, $value, $limit = 1, $unpublished = false)
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
    public static function findOneByField($field, $value, $object = null, $unpublished = false)
    {
        $list = \Pimcore\Model\DataObject\VariantProduct::findByField($field, $value, 1, $unpublished);
        if (!empty($list) && is_array($list)) {
            foreach ($list as $item) {
                if (!$object || !$object instanceof \Pimcore\Model\DataObject\VariantProduct) {
                    return $item;
                }
                if ($object->getId() !== $item->getId()) {
                    return $item;
                }
            }
        }
        return null;
    }

    public static function addUpdateVariant($variant, $importFlag, $updateFlag, $marketplace, $parent)
    {
        $object = null;
        if (isset($variant['tmp_uniqueMarketplaceId'])) {
            $object = \Pimcore\Model\DataObject\VariantProduct::findOneByField(
                'uniqueMarketplaceId',
                $variant['tmp_uniqueMarketplaceId'],
                unpublished: true
            );
        } 
        if (!$object) {
            $object = \Pimcore\Model\DataObject\VariantProduct::findOneByField(
                'uniqueMarketplaceId',
                $variant['uniqueMarketplaceId'],
                unpublished: true
            );
        }
        if (!$object) {
            if (!$importFlag) {
                return true;
            }
            $object = new \Pimcore\Model\DataObject\VariantProduct();
        }
        return $object->updateVariant($variant, $updateFlag, $marketplace, $parent);
    }

    public function jsonRead($fieldName)
    {
        $db = \Pimcore\Db::get();
        try {
            return $db->fetchOne("SELECT json FROM iwa_json_store WHERE object_id = ? AND field_name = ?", [$this->getId(), $fieldName]);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function jsonWrite($fieldName, $data)
    {
        $db = \Pimcore\Db::get();
        $stmt = $db->prepare("INSERT INTO iwa_json_store (object_id, field_name, json_data) 
            VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json_data = ?");
        $stmt->execute([$this->getId(), $fieldName, $data, $data]);
    }

    public function updateVariant($variant, $updateFlag, $marketplace, $parent)
    {
        if (!$updateFlag) {
            return true;
        }
        if (!$marketplace instanceof Marketplace) {
            throw new \Exception('Marketplace is required for adding/updating VariantProduct');
        }
        if (!$parent) {
            throw new \Exception('Parent is required for adding/updating VariantProduct');
        }
        $key_base = "{$marketplace->getKey()} {$variant['title']} ";
        $key_base.= $variant['attributes'] ?? '';
        $key_base = Utility::sanitizeVariable($key_base,250);
        $key = '';
        while (self::findOneByField('key', "$key_base$key", $this, unpublished: true)) {
            $key = $key ? $key+1 : 1;
        }
        $this->setKey(trim("$key_base$key"));
        $this->setImageUrl($variant['imageUrl'] ?? null);
        $this->setUrlLink($variant['urlLink'] ?? null);
        $this->setSalePrice($variant['salePrice'] ?? '');
        $this->setSaleCurrency($variant['saleCurrency'] ?? '');
        $this->setTitle($variant['title'] ?? '');
        $this->setAttributes($variant['attributes'] ?? '');
        $this->setUniqueMarketplaceId($variant['uniqueMarketplaceId'] ?? '');
        $this->setAmazonAsin($variant['amazonAsin'] ?? '');
        $this->setMarketplace($marketplace);
        $this->setParent($parent);
        $this->setPublished($variant['published'] ?? false);
        try {
            $result = $this->save();
        } catch (\Throwable $e) {
            echo "\n**************************************************\n";
            print_r($variant);
            echo "Error: {$e->getMessage()}\n";
            echo "\n**************************************************\n";
            return false;
        }
        if ($result) {
            echo "{$this->getId()} ";
            if (!empty($variant['apiResponseJson'])) {
                $this->jsonWrite('apiResponseJson', $variant['apiResponseJson'] ?? '');
            }
            if (!empty($variant['parentResponseJson'])) {
                $this->jsonWrite('parentResponseJson', $variant['parentResponseJson'] ?? '');
            }
        }
        return $result;    
    }

    public function fixImageCache(array $listingImageList, $variantImage = null)
    {
        if (empty($listingImageList)) {
            return;
        }
        $items = [];
        foreach ($listingImageList as $asset) {
            if (empty($asset)) {
                continue;
            }
            $advancedImage = new \Pimcore\Model\DataObject\Data\Hotspotimage();
            $advancedImage->setImage($asset);
            $items[] = $advancedImage;
        }
        $this->setImageGallery(new \Pimcore\Model\DataObject\Data\ImageGallery($items));
        $variantImage = $variantImage ?? reset($listingImageList);
        if ($variantImage instanceof Asset/Image) {
            $urlImage = new \Pimcore\Model\DataObject\Data\ExternalImage(
                "https://mesa.iwa.web.tr/var/assets/".str_replace(" ", "%20", $variantImage->getFullPath())
            );
            $this->setImageUrl($urlImage);
        }
        $this->save();
    }
}