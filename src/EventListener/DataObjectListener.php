<?php

namespace App\EventListener;

use App\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\CustomLayout;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Serial;
use Pimcore\Model\DataObject\GroupProduct;
use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\Dependency;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

use Pimcore\Event\Model\DataObjectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Pimcore\Cache;
use Symfony\Component\EventDispatcher\GenericEvent;

use Pimcore\Model\DataObject\Service;

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
            'pimcore.admin.resolve.elementAdminStyle' => 'onResolveElementAdminStyle',
            'pimcore.admin.dataobject.get.preSendData' => 'onPreSendData',
        ];
    }

    protected function doModifyCustomLayouts(Product $object, GenericEvent $event): void
    {
        $level = $object->level();
        $data = $event->getArgument('data');
        if (empty($data['validLayouts'])) {
            return;
        }
        foreach ($data['validLayouts'] as $key=>$layout) {
            if (($layout['name'] === 'product' && $level == 0) || ($layout['name'] === 'variant' && $level == 1)) {
                $data['currentLayoutId'] = $layout['id'];
                $customLayout = CustomLayout::getById($layout['id']);
                $data['layout'] = $customLayout->getLayoutDefinitions();
                Service::enrichLayoutDefinition($data['layout'], $object);
            } else {
                unset($data['validLayouts'][$key]);
            }
        }
        $event->setArgument('data', $data);
    }

    public function onPreSendData(GenericEvent $event): void
    {
        $object = $event->getArgument('object');
        if ($object instanceof Product) {
            $this->doModifyCustomLayouts($object, $event);
        }
    }

    /**
     * @throws \Exception
     */
    public function onPreDelete(DataObjectEvent $event): void
    {
        $object = $event->getObject();
        if ($object instanceof Folder) {
            Product::setGetInheritedValues(false);
            $parent = $object->getParent();
            if ($object->getKey() === 'Ayarlar' || ($parent && $parent->getKey() === 'Ayarlar')) {
                throw new \Exception('Ayarlar klasörü ve altındaki ana klasörler silinemez');
            }
        }
        if ($object instanceof Product) {
            if ($object->getDependencies()->getRequiredByTotalCount()) {
                throw new \Exception('Bu ürün muhtemelen bir setin parçası. Silinemez!'.json_encode($object->getDependencies()->getRequiredBy()));
            }
        }
    }

    public function onResolveElementAdminStyle(\Pimcore\Bundle\AdminBundle\Event\ElementAdminStyleEvent $event): void
    {
        $object = $event->getElement();
        if (
            $object instanceof Product || 
            $object instanceof VariantProduct
        ) {
            $event->setAdminStyle(new \App\Model\AdminStyle\ProductAdminStyle($object));
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
                if ($listingItem->getImageUrl() instanceof \Pimcore\Model\DataObject\Data\ExternalImage) {
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
        $image_url = '';
        if ($object instanceof Product) {
            Product::setGetInheritedValues(false);
//            if (empty($object->getImageUrl())) {
                $image_url = self::traverseProducts($object);
                if (!empty($image_url)) {
                    $object->setImageUrl(new \Pimcore\Model\DataObject\Data\ExternalImage($image_url));
                } else {
                    $object->setImageUrl(null);
                }
//            }
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
