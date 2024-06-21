<?php

namespace App\EventListener;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Product;
use Pimcore\Event\Model\DataObjectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Model\Traits\ProductTrait;
use Pimcore\Cache;

class DataObjectListener implements EventSubscriberInterface
{
    use ProductTrait;

    public static function getSubscribedEvents()
    {
        return [
            'pimcore.dataobject.preAdd' => 'onPreAdd',
            'pimcore.dataobject.preUpdate' => 'onPreUpdate',
            'pimcore.dataobject.postUpdate' => 'onPostUpdate',
        ];
    }

    public function onPostUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if ($object instanceof ProductClass || $object instanceof Product) {
            Cache::remove('productClasses');
            return;
        }
        if ($object instanceof Brand) {
            Cache::remove('brands');
            return;
        }
        if ($object instanceof PricingNode) {
            Cache::remove('pricingNodes');
            return;
        }
        if ($object instanceof CostNode) {
            Cache::remove('costNodes');
            return;
        }
    }

    public function onPreAdd(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if ($object instanceof Product) {
            if (!$object->getProductCode()) {
                $object->setProductCode($this->generateUniqueCode());
            }
            $objectType = $object->getType();
            if ($objectType === DataObject::OBJECT_TYPE_VARIANT) {
                $parent = $object->getParent();
                $grandParent = $parent ? $parent->getParent() : null;
                if ($grandParent instanceof Product) {
                    $object->setVariationSize($object->getKey());
                } else {
                    $object->setVariationColor($object->getKey());
                }
            }
        }
    }

    public function onPreUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if (!$object instanceof Product) {
            return;
        }
        $objectType = $object->getType();
        if ($objectType === DataObject::OBJECT_TYPE_VARIANT) {
            $parent = $object->getParent();
            $grandParent = $parent ? $parent->getParent() : null;
            if ($grandParent instanceof Product) {
                $object->setVariationSize($object->getKey());
                if ($object->getIwaskuActive() && empty($object->getIwasku())) {
                    $iwasku = "{$grandParent->getProductClass()}_{$grandParent->getProductCode()}_{$object->getProductCode()}";
                    $object->setIwasku($iwasku);             
                }
            } else {
                $object->setVariationColor($object->getKey());
            }
            return;
        }
        $productClass = $object->getProductClass();
        if (empty($productClass)) {
            return;
        }
        $targetFolder = DataObject::getByPath('/Ürünler/' . $productClass);
        if (!$targetFolder) {
            $targetFolder = new DataObject\Folder();
            $targetFolder->setKey($productClass);
            $targetFolder->setParent(DataObject::getByPath('/Ürünler'));
            $targetFolder->save();
        }
        $object->setParent($targetFolder);
    }

}
