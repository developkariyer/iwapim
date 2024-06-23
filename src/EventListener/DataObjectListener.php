<?php

namespace App\EventListener;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\CustomLayout;
use Pimcore\Model\DataObject\Product;
use Pimcore\Event\Model\DataObjectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Pimcore\Cache;
use Symfony\Component\EventDispatcher\GenericEvent;
use \Pimcore\Model\DataObject\Service;

class DataObjectListener implements EventSubscriberInterface
{

    private $postAddRunning = false;

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'pimcore.dataobject.preAdd' => 'onPreAdd',
            'pimcore.dataobject.postAdd' => 'onPostAdd',
            'pimcore.dataobject.preUpdate' => 'onPreUpdate',
            'pimcore.dataobject.postUpdate' => 'onPostUpdate',
            'pimcore.admin.dataobject.get.preSendData' => 'onPreSendData',
        ];
    }

    /**
     * Called before a data object is sent to Frontend/Backend UX.
     * Limits layout based on Product/Variant level
     *
     * @param GenericEvent $event
     */
    public function onPreSendData(GenericEvent $event)
    {
        error_log('Triggered onPreSendData');
        $object = $event->getArgument('object');
        if ($object instanceof Product) {
            $data = $event->getArgument('data');
            $level = $object->getLevel();
            $validLayouts = $data['validLayouts'];
            $newLayouts = [];
            foreach ($validLayouts as $layout) {
                if ($level === $layout['name']) {
                    $newLayouts[] = $layout;
                }
            }
            if (empty($newLayouts)) {
                $newLayouts[] = [
                    'id' => 0,
                    'name' => 'Main',
                ];
            }
            $data['currentLayoutId'] = $newLayouts[0]['id'];
            $customLayout = CustomLayout::getById($data['currentLayoutId']);
            $data['layout'] = $customLayout->getLayoutDefinitions();
            Service::enrichLayoutDefinition($data['layout']);
            $data['validLayouts'] = array_values($newLayouts);
            $event->setArgument("data", $data);
        }
    }

    /**
     * Called after sending object to database
     * Used for invalidating cache objects for Frontend UX template elements
     * 
     * @param DataObjectEvent $event
     */
    public function onPostUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();
        error_log('Triggered onPostUpdate from '.$object->getKey());
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
            error_log('Triggered onPreAdd from '.$object->getKey().' with level of '.$object->getLevel());
            $object->checkProductCode();
            switch ($object->getLevel()) {
                case $object::COLOR_VARIANT:
                    $object->setVariationColor($object->getKey());
                    $object->setType(\Pimcore\Model\DataObject\AbstractObject::OBJECT_TYPE_VARIANT); 
                    break;
                case $object::SIZE_VARIANT:
                    $object->setVariationSize($object->getKey());
                    $object->setType(\Pimcore\Model\DataObject\AbstractObject::OBJECT_TYPE_VARIANT); 
                    break;
                case $object::MAIN_PRODUCT:
                    if (empty($object->getName())) {
                        $object->setName($object->getKey());
                    }
                    if (empty($object->getProductClass())) {
                        $object->setProductClass('TASLAK');
                    }
            }    
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
            error_log('Triggered onPreUpdate from '.$object->getKey().' with level of '.$object->getLevel());
            switch ($object->getLevel()) {
                case $object::MAIN_PRODUCT:
                    $productClass = $object->getProductClass();
                    if (empty($productClass)) {
                        return;
                    }
                    $targetFolder = DataObject\Folder::getByPath('/Ürünler/' . $productClass);
                    if (!$targetFolder) {
                        $targetFolder = new DataObject\Folder();
                        $targetFolder->setKey($productClass);
                        $targetFolder->setParent(DataObject\Folder::getByPath('/Ürünler'));
                        $targetFolder->save();
                    }
                    $object->setParent($targetFolder);
                    break;
                case $object::COLOR_VARIANT:
                    $object->setType(\Pimcore\Model\DataObject\AbstractObject::OBJECT_TYPE_VARIANT); 
                    $object->setVariationColor($object->getKey());
                    $object->setPublished(false);
                    break;
                case $object::SIZE_VARIANT:
                    $object->setType(\Pimcore\Model\DataObject\AbstractObject::OBJECT_TYPE_VARIANT); 
                    $object->setVariationSize($object->getKey());
                    if ($object->getIwaskuActive() && empty($object->getIwasku())) {
                        $grandParent = $object->getParent()->getParent();
                        $productClass = $grandParent->getProductClass();
                        if ($productClass !== 'TASLAK') {
                            $iwasku = "{$productClass}_{$grandParent->getProductCode()}_{$object->getParent()->getProductCode()}_{$object->getProductCode()}";
                            $object->setIwasku($iwasku);             
                        } else {
                            $object->setIwaskuActive(null);
                        }
                    }
            }
        }
        error_log('onPreUpdate finished');
    }

    /**
     * Called after adding an object to database
     * Used to create missing color and size variants
     * 
     * @param DataObjectEvent $event
     */
    public function onPostAdd(DataObjectEvent $event)
    {
        if ($this->postAddRunning) return;
        $this->postAddRunning = true;
        $object = $event->getObject();
        if ($object instanceof Product) {
            error_log('Triggered onPostAdd from '.$object->getKey().' with level of '.$object->getLevel());
            switch ($object->getLevel()) {
                case $object::COLOR_VARIANT:
                    $this->addSizesToColorVariant($object);
                    break;
                case $object::SIZE_VARIANT:
                    $this->addSizeVariantToColors($object);
                    break;
                case $object::MAIN_PRODUCT:
            }
        }
        $this->postAddRunning = false;
    }

    private function addSizesToColorVariant($colorVariant)
    {
        error_log('Adding all sizes to color variant');
        $product = $colorVariant->getParent();
        $sizes = [];
        foreach ($product->getChildren(includingUnpublished:true) as $colorVariants) {
            foreach ($colorVariants->getChildren() as $sizeVariants) {
                $sizes[$sizeVariants->getKey()] = true; //get a complete list of current sizes
                error_log('Size read: '.$sizeVariants->getKey());
            }
        }
        foreach ($colorVariant->getChildren() as $sizeVariant) {
            $sizes[$sizeVariant->getKey()] = false; //remove already existing size variants
        }
        foreach ($sizes as $key=>$value) {
            if ($value) {
                error_log('Adding size '.$key);
                $colorVariant->addSize($key);
            }
        }
    }

    private function addSizeVariantToColors($sizeVariant)
    {
        error_log('Adding new size to all color variant');
        $colorVariant = $sizeVariant->getParent();
        $product = $colorVariant->getParent();
        error_log('New size belongs to '.$product->getKey().' with color of '.$colorVariant->getKey());
        foreach ($product->getChildren(includingUnpublished:true) as $colorVariants) {
            $tempFlag = true;
            foreach ($colorVariants->getChildren() as $sizeVariants) {
                if ($sizeVariants->getKey() === $sizeVariant->getKey()) {
                    $tempFlag = false;
                }
            }
            if ($tempFlag) {
                error_log('Adding '.$sizeVariant->getKey().' size to color '.$colorVariants->getKey());
                $colorVariants->addSize($sizeVariant->getKey());
            }
        }
    }

}
