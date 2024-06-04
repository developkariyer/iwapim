<?php

namespace App\EventListener;

use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Product\Listing;
use Pimcore\Event\Model\DataObjectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductListener implements EventSubscriberInterface
{

    private function picturePath($object)
    {
        if ($object instanceof Product) {
            return '/products/' . $object->getProductCode() . '/images/';
        }
        return null;
    }

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
            if ($picture = $object->getPicture()) {
                $picture->setUploadPath($this->picturePath($object);
            }
        }
    }

    public function onPreUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();

        if ($object instanceof Product) {
            if (!$object->getIwasku() && $object->getIwaskuActive()) {
                $topMostProduct = $this->getTopMostProduct($object);
                if (empty($topMostProduct)) {
                    $topMostProduct = $object;
                }
                $iwasku = "{$topMostProduct->getProductClass()}_{$topMostProduct->getProductCode()}_{$object->getProductCode()}";
                $object->setIwasku($iwasku);
            }
            if ($picture = $object->getPicture()) {
                $picture->setUploadPath($this->picturePath($object);
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
