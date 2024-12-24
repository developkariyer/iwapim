<?php

namespace App\Model\AdminStyle;

use Pimcore\Model\Element\AdminStyle;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\VariantProduct;

class ProductAdminStyle extends AdminStyle
{
    /** @var ElementInterface */
    protected ElementInterface $element;

    public function __construct($element)
    {
        parent::__construct($element);

        $this->element = $element;

        if ($element instanceof Product) {
            $this->elementIcon = '/custom/product.svg';
            if ($element->level() === 1) {
                $this->elementIcon = '/custom/object.svg';
                if (count($element->getListingItems()) + count($element->getBundleProducts())) {
                    $this->elementIcon = '/custom/deployment.svg';
                }
            }
        }
        if ($element instanceof VariantProduct) {
            $this->elementIcon = match (count($this->element->getMainProduct())) {
                0 => '/bundles/pimcoreadmin/img/flat-color-icons/list.svg', //'/custom/listing.svg',
                1 => '/custom/listing_ok.svg',
                default => '/custom/listing_fail.svg',
            };
        }
    }

    public function getElementQtipConfig(): ?array
    {
        if ($this->element instanceof Product) {
            $config = parent::getElementQtipConfig();
            $config['title'] = "{$this->element->getId()}: {$this->element->getName()}";
            $shopifyVariations = $total = count($this->element->getListingItems());
            foreach ($this->element->getChildren() as $child) {
                if ($child instanceof Product) {
                    $shopifyVariations += count($child->getListingItems());
                }
            }
            $config['text'] = "$total/$shopifyVariations listing bağlı<br>";
            $image = $this->element->getInheritedField('image');
            if ($image) {
                $config["text"] .= "<img src='{$image->getThumbnail()->getPath()}' style='max-width: 100%; height: 100px; background-color: #f0f0f0;' alt='alt'>";
            }
            $album = $this->element->getInheritedField('album');
            foreach ($album as $asset) {
                if (!$asset) {
                    continue;
                }
                $image = $asset->getImage();
                if ($image) {
                    $config['text'] .= "<img src='{$image->getThumbnail()->getPath()}' style='max-width: 100%; height: 100px; background-color: #f0f0f0;' alt='alt'>";
                    break;
                }
            }
            $imageUrl = $this->element->getInheritedField('imageUrl');
            if ($imageUrl) {
                $config['text'] .= "<img src='{$imageUrl->getUrl()}' style='max-width: 100%; height: 100px; background-color: #f0f0f0;' alt=\"alt\">";
            }
            return $config;
        }
        if ($this->element instanceof VariantProduct) {
            $config = parent::getElementQtipConfig();
            $config['text'] = '';
            if ($this->element->getImageUrl()) {
                $config['text'] .= "<br><img src='{$this->element->getImageUrl()->getUrl()}' style='max-width: 100%; height: 100px; background-color: #f0f0f0;' alt=\"alt\">";
            }
            $config['text'] .= "<br>{$this->element->getUniqueMarketplaceId()}<br>{$this->element->getAttributes()}";
            return $config;
        }
        return parent::getElementQtipConfig();
    }
}