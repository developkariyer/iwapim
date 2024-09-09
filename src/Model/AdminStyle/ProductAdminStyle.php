<?php

namespace App\Model\AdminStyle;

use App\Website\Tool\ForceInheritance;
use Pimcore\Model\Element\AdminStyle;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\VariantProduct;

class ProductAdminStyle extends AdminStyle
{
    /** @var ElementInterface */
    protected $element;

    public function __construct($element)
    {
        parent::__construct($element);

        $this->element = $element;

        if ($element instanceof Product) {
            switch ($element->level()) {
                case 0:
                    $this->elementIcon = '/custom/navyobject.svg';
                    break;
                case 1:
                    $this->elementIcon = (count($element->getListingItems())) ? '/custom/deployment.svg' : '/custom/object.svg';
                    break;
                default:
            }
        }
        if ($element instanceof VariantProduct) {
            $this->elementIcon = (count($this->element->getMainProduct())) ? '/bundles/pimcoreadmin/img/flat-color-icons/accept_database.svg' : '/bundles/pimcoreadmin/img/flat-color-icons/list.svg';
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
                $config["text"] .= "<img src='{$image->getThumbnail()->getPath()}' style='max-width: 100%; height: 100px; background-color: #f0f0f0;'>";
            }
            $album = $this->element->getInheritedField('album');
            foreach ($album as $asset) {
                if (!$asset) {
                    continue;
                }
                $image = $asset->getImage();
                if ($image) {
                    $config['text'] .= "<img src='{$image->getThumbnail()->getPath()}' style='max-width: 100%; height: 100px; background-color: #f0f0f0;'>";
                    break;
                }
            }
            $imageUrl = $this->element->getInheritedField('imageUrl');
            if ($imageUrl) {
                $config['text'] .= "<img src='{$imageUrl->getUrl()}' style='max-width: 100%; height: 100px; background-color: #f0f0f0;'>";
            }
            return $config;
        }
        if ($this->element instanceof VariantProduct) {
            $config = parent::getElementQtipConfig();
            if ($this->element->getImageUrl()) {
                $config['text'] = "<br><img src='{$this->element->getImageUrl()->getUrl()}' style='max-width: 100%; height: 100px; background-color: #f0f0f0;'>";
            }
            return $config;
        }
        return parent::getElementQtipConfig();
    }
}