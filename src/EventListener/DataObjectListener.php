<?php

namespace App\EventListener;

use App\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\CustomLayout;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Serial;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

use Pimcore\Event\Model\DataObjectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Pimcore\Cache;
use Symfony\Component\EventDispatcher\GenericEvent;

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

    public function onPreSendData(GenericEvent $event)
    {
        $object = $event->getArgument('object');
        if ($object instanceof Product) {
            $level = $object->level();
            $data = $event->getArgument('data');
            $dataChanged = false;
            foreach ($data['validLayouts'] ?? [] as $layout) {
                if (($layout['name'] === 'product' && $level == 0) || ($layout['name'] === 'variant' && $level == 1)) {
                    $data['currentLayoutId'] = $layout['id'];
                    $dataChanged = true;
                    break;
                }
            }
            if ($dataChanged) {
                $event->setArgument('data', $data);
            }
        }
    }

    public function onPreDelete(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if ($object instanceof Folder) {
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
            $object->checkIwasku();
            $object->checkProductCode();
            $object->checkProductIdentifier();
            $object->checkKey();
            if ($object->getParent() instanceof Product) {
                $object->nullify();
            }
            if ($object->level() == 1) {
                if ($object->getName()) {
                    throw new \Exception('Varyasyon seviyesinde isim değiştirilemez.');
                }
                if (!($object->getVariationSize() || $object->getVariationColor())) {
                    throw new \Exception('Varyasyon seviyesinde renk veya ebat belirtilmelidir.');
                }
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
/*            $image_url = self::traverseProducts($object);
            if (!empty($image_url)) {
                $object->setImageUrl(new \Pimcore\Model\DataObject\Data\ExternalImage($image_url));
            } else {
                $object->setImageUrl(null);
            }*/
            if (!$object->getParent() instanceof Product) {
                [$sizes, $colors] = $object->listVariations();
                $object->setVariationSizeList(implode("\n", $sizes));
                $object->setVariationColorList(implode("\n", $colors));
            } else {
                $object->setVariationSizeList('');
                $object->setVariationColorList('');
            }
        }
    }

    private function doModifyCustomLayouts(array $data, Product $object, int $customLayoutToSelect, array $layoutsToRemove): array
    {
        $data['currentLayoutId'] = $customLayoutToSelect;
        $customLayout = CustomLayout::getById($customLayoutToSelect);
        $data['layout'] = $customLayout->getLayoutDefinitions();
        Service::enrichLayoutDefinition($data['layout'], $object);
        
        if (!empty($layoutsToRemove)) {
            //remove main layout from valid layouts
            $validLayouts = $data['validLayouts'];
            foreach($validLayouts as $key => $validLayout) {
                if(in_array($validLayout['id'], $layoutsToRemove)) {
                    unset($validLayouts[$key]);
                }
            }
            $data['validLayouts'] = array_values($validLayouts);            
        }
        return $data; 
    }

}
