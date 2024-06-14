<?php

namespace App\EventListener;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Product\Listing;
use Pimcore\Event\Model\DataObjectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductListener implements EventSubscriberInterface
{

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
        if ($object instanceof Product) {
            $productClass = $object->getProductClass();
            $targetFolderPath = '/Ürünler';
            if (!empty($productClass)) {
                $targetFolderPath .= '/' . $productClass;
                $targetFolder = DataObject::getByPath($targetFolderPath);
                if (!$targetFolder) {
                    $targetFolder = new DataObject\Folder();
                    $targetFolder->setKey($productClass);
                    $targetFolder->setParent(DataObject::getByPath('/Ürünler'));
                    $targetFolder->save();
                }
            } else {
                $targetFolder = DataObject::getByPath($targetFolderPath);
            }
            $object->setParent($targetFolder);
            $object->save();
        }
    }

    public function onPreAdd(DataObjectEvent $event)
    {
        $object = $event->getObject();

        if ($object instanceof Product) {
            if (!$object->getProductCode()) {
                $object->setProductCode($this->generateUniqueCode());
            }
        }
    }

    public function onPreUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();

        if ($object instanceof Product) {
            if (!$object->getIwasku() && $object->getProductClass() && $object->getIwaskuActive()) {
                $topMostProduct = $this->getTopMostProduct($object);
                if (empty($topMostProduct)) {
                    $topMostProduct = $object;
                }
                $iwasku = "{$topMostProduct->getProductClass()}_{$topMostProduct->getProductCode()}_{$object->getProductCode()}";
                $object->setIwasku($iwasku);
            }
        }
    }

    private function getTopMostProduct(Product $object)
    {
        $parent = $object;
        $topMostProduct = null;
        while ($parent = $parent->getParent()) {
            if ($parent instanceof Product) {
                $topMostProduct = $parent;
            }
        }
        return $topMostProduct;
    }

    private function generateCustomString($length = 6) {
        $characters = 'ABCDEFGHJKMNPQRSTVWXYZ123456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = mt_rand(0, $charactersLength - 1);
            $randomString .= $characters[$randomIndex];
        }

        return $randomString;
    }

    private function generateUniqueCode()
    {
        while (true) {
            $candidateCode = $this->generateCustomString(6);
            if (!$this->isProductCodeExists($candidateCode)) {
                return $candidateCode;
            }
        }
    }

    private function isProductCodeExists($productCode)
    {
        $listing = new Listing();
        $listing->setCondition('productCode = ?', [$productCode]);
        return $listing->count() > 0;
    }

}
