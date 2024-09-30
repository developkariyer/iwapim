<?php

namespace App\EventListener;

use App\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\CustomLayout;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Serial;
use Pimcore\Model\DataObject\GroupProduct;
use Pimcore\Model\DataObject\Data\Link;

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

    protected function doModifyCustomLayouts(Product $object, GenericEvent $event)
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

    public function onPreSendData(GenericEvent $event)
    {
        $object = $event->getArgument('object');
        if ($object instanceof Product) {
            $this->doModifyCustomLayouts($object, $event);
        }
    }

    public function onPreDelete(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if ($object instanceof Folder) {
            Product::setGetInheritedValues(false);
            $parent = $object->getParent();
            if ($object->getKey() === 'Ayarlar' || ($parent && $parent->getKey() === 'Ayarlar')) {
                throw new \Exception('Ayarlar klasörü ve altındaki ana klasörler silinemez');
            }
        }
    }

    public function onResolveElementAdminStyle(\Pimcore\Bundle\AdminBundle\Event\ElementAdminStyleEvent $event)
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
    public function onPreAdd(DataObjectEvent $event)
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
    public function onPreUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if ($object instanceof Product) {
            Product::setGetInheritedValues(false);
            $object->checkIwasku();
            $object->checkProductCode();
            $object->checkProductIdentifier();
            $object->checkKey();
            if ($object->getParent() instanceof Product) {
                $object->nullify();
            }
        }
        if ($object instanceof Serial) {
            $object->checkLabel();
        }
    }

    public function onPostUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if ($object instanceof Product) {
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
        return "";
    }

    public function onPostLoad(DataObjectEvent $event)
    {
        $object = $event->getObject();
        $image_url = '';
        if ($object instanceof Product) {
            Product::setGetInheritedValues(false);
//            if (!$object->getImageUrl() instanceof \Pimcore\Model\DataObject\Data\ExternalImage) {
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
            }
            if ($object->level()==0) {
                [$sizes, $colors] = $object->listVariations();
                $object->setVariationSizeList(implode("\n", $sizes));
                $object->setVariationColorList(implode("\n", $colors));

                $l = new Link();
                $l->setPath('https://iwa.web.tr/report/product/' . $object->getId());
                $object->setReportLink($l);
            }
        }
        if ($object instanceof GroupProduct) {
            $object->setFrontendUrl(self::generateLink('https://iwa.web.tr/report/group/' . $object->getId()));
            $object->setIwaskuStickers(self::generateLink('https://iwa.web.tr/report/sticker/' . $object->getId() . '/iwasku'));
            $object->setEuStickers(self::generateLink('https://iwa.web.tr/report/sticker/' . $object->getId() . '/eu'));
            $object->setUsStickers(self::generateLink('https://iwa.web.tr/report/sticker/' . $object->getId() . '/us'));
        }
    }

    protected static function generateLink($url)
    {
        $l = new Link();
        $l->setPath($url);
        return $l;
    }

}
