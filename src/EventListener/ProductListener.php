<?php

namespace App\EventListener;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Product;
use Pimcore\Event\Model\DataObjectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Model\Traits\ProductTrait;

class ProductListener implements EventSubscriberInterface
{
    use ProductTrait;

    public static function getSubscribedEvents()
    {
        return [
            'pimcore.dataobject.preAdd' => 'onPreAdd',
            'pimcore.dataobject.preUpdate' => 'onPreUpdate',
        ];
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
