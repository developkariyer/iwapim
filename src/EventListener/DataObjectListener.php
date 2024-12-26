<?php

namespace App\EventListener;

use App\Model\DataObject\VariantProduct;
use Exception;
use Pimcore\Model\DataObject\Data\ExternalImage;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Serial;
use Pimcore\Model\DataObject\GroupProduct;
use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\Dependency;

use Pimcore\Event\Model\DataObjectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DataObjectListener implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'pimcore.dataobject.preDelete' => 'onPreDelete',
            'pimcore.dataobject.preAdd' => 'onPreAdd',
            'pimcore.dataobject.preUpdate' => 'onPreUpdate',
            'pimcore.dataobject.postUpdate' => 'onPostUpdate',
            'pimcore.dataobject.postLoad' => 'onPostLoad',
        ];
    }

    /**
     * @throws Exception
     */
    public function onPreDelete(DataObjectEvent $event): void
    {
        $object = $event->getObject();
        if ($object instanceof Folder) {
            Product::setGetInheritedValues(false);
            $parent = $object->getParent();
            if ($object->getKey() === 'Ayarlar' || ($parent && $parent->getKey() === 'Ayarlar')) {
                throw new Exception('Ayarlar klasörü ve altındaki ana klasörler silinemez');
            }
        }
        if ($object instanceof Product) {
            if ($object->getDependencies()->getRequiredByTotalCount()) {
                error_log(json_encode($object->getDependencies()->getRequiredBy()));
                throw new Exception('Bu ürün muhtemelen bir setin parçası. Silinemez');
            }
        }
    }


    /**
     * Called before initializing a new object
     * Used to set productCode, variationColor and variationSize values
     * 
     * @param DataObjectEvent $event
     */
    public function onPreAdd(DataObjectEvent $event): void
    {
        $object = $event->getObject();
        if ($object instanceof Product) {
            Product::setGetInheritedValues(false);
            $object->checkProductCode();
        }
    }

    /**
     * Called before saving an object to database
     * Used for setting object folder
     * Used for setting Iwasku when set active
     * 
     * $param DataObjectEvent $event
     */
    public function onPreUpdate(DataObjectEvent $event): void
    {
        $object = $event->getObject();
        if ($object instanceof Product && $object->getParent()->getKey() !== 'WISERSELL ERROR') {
            Product::setGetInheritedValues(false);
            if ($object->getParent() instanceof Product) {
                $object->nullify();
            }
            $object->checkIwasku();
            $object->checkProductCode();
            $object->checkProductIdentifier();
            $object->checkKey();
        }
        if ($object instanceof Serial) {
            $object->checkLabel();
        }
    }

    public function onPostUpdate(DataObjectEvent $event): void
    {
        $object = $event->getObject();
        if ($object instanceof Product && $object->getParent()->getKey() !== 'WISERSELL ERROR') {
            Product::setGetInheritedValues(false);
            if (!$object->getParent() instanceof Product) {
                $object->checkVariations();
            }
            $object->checkAssetFolders();
        }
    }

    private static function traverseProducts($object)
    {
        $listingItems = $object->getListingItems();
        foreach ($listingItems as $listingItem) {
            if (($listingItem instanceof VariantProduct)) {
                if ($listingItem->getImageUrl() instanceof ExternalImage) {
                    return $listingItem->getImageUrl()->getUrl();
                }
            }
        }
        $children = $object->getChildren();
        foreach ($children as $child) {
            if ($child instanceof Product) {
                $image = self::traverseProducts($child);
                if (!empty($image)) {
                    return $image;
                }
            }
        }
        $dependencyObject = Dependency::getBySourceId($object->getId(), 'object');
        $dependencies = $dependencyObject->getRequiredBy();
        foreach ($dependencies as $dependency) {
            if ($dependency['type'] == 'object') {
                $object = Product::getById($dependency['id']);
                if ($object instanceof Product) {
                    $image = self::traverseProducts($object);
                    if (!empty($image)) {
                        return $image;
                    }
                }
            }
        }
        return "";
    }

    public function onPostLoad(DataObjectEvent $event): void
    {
        $object = $event->getObject();
        if ($object instanceof Product) {
            Product::setGetInheritedValues(false);
            $image_url = self::traverseProducts($object);
            if (!empty($image_url)) {
                $object->setImageUrl(new ExternalImage($image_url));
            } else {
                $object->setImageUrl(null);
            }
            if ($object->level()>0) {
                $object->setTechnicals(null);
                $object->setVariationSizeList(null);
                $object->setVariationColorList(null);
            } else {
                [$sizes, $colors] = $object->listVariations();
                $object->setVariationSizeList(implode("\n", $sizes));
                $object->setVariationColorList(implode("\n", $colors));
                if (empty($object->getReportLink())) {
                    $object->setReportLink(static::generateLink('https://iwa.web.tr/report/product/' . $object->getId()));
                }
            }
        }
        if ($object instanceof GroupProduct) {
            $object->setFrontendUrl(self::generateLink('https://iwa.web.tr/report/group/' . $object->getId()));
            $object->setIwaskuStickers(self::generateLink('https://iwa.web.tr/report/sticker/' . $object->getId() . '/iwasku'));
            $object->setEuStickers(self::generateLink('https://iwa.web.tr/report/sticker/' . $object->getId() . '/eu'));
            $object->setUsStickers(self::generateLink('https://iwa.web.tr/report/sticker/' . $object->getId() . '/us'));
        }
    }

    protected static function generateLink($url): Link
    {
        $l = new Link();
        $l->setPath($url);
        return $l;
    }

}
