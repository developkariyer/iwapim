<?php

namespace App\Connector\Marketplace\Amazon;

use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject\Fieldcollection\Data\AmazonMarketplace;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Folder;

use Carbon\Carbon;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
use App\Utils\Utility;
use App\Utils\Registry;

class Import
{
    public $amazonConnector;

    public $iwaskuList = [];

    public function __construct(AmazonConnector $amazonConnector) 
    {
        $this->amazonConnector = $amazonConnector;
    }

    
    private function getAttributes($listing) {
        $title = $listing['item-name'];
        if (preg_match('/\(([^()]*)\)[^\(]*$/', $title, $matches)) {
            return trim($matches[1]);
        }
        return '';    
    }

    private function getTitle($listing)
    {
        return trim(str_replace('('.$this->getAttributes($listing).')','',$listing['item-name'] ?? ''));
    }

    private function getFolder($asin): Folder
    {
        $folder = Utility::checkSetPath("Amazon", Utility::checkSetPath('Pazaryerleri'));

        $json = Utility::retrieveJsonData($asin);
        if (!empty($json) && !empty($json['classifications'][0]['classifications'][0]['displayName'])) {
            $folderTree = [];
            $parent = $json['classifications'][0]['classifications'][0];
            while (!empty($parent['displayName'])) {
                if (!in_array($parent['displayName'], ['Categories', 'Subjects', 'Departments'])) {
                    $folderTree[] = $parent['displayName'];
                }
                $parent = $parent['parent'] ?? [];
            }
            while (!empty($folderTree)) {
                $folder = Utility::checkSetPath(array_pop($folderTree), $folder);
            }
            return $folder;
        }
        return Utility::checkSetPath(
            '00 Yeni ASIN',
            $folder
        );
    }

    protected function checkIwasku($iwasku)
    {
        if (empty($this->iwaskuList)) {
            $db = \Pimcore\Db::get();
            $this->iwaskuList = $db->fetchFirstColumn("SELECT DISTINCT iwasku FROM object_store_product WHERE iwasku IS NOT NULL ORDER BY iwasku");
            $this->iwaskuList = array_filter( $this->iwaskuList);
        }
        return in_array($iwasku, $this->iwaskuList);
    }

    public function import($updateFlag, $importFlag)
    {
        $total = count($this->amazonConnector->listings);
        $index = 0;
        foreach ($this->amazonConnector->listings as $asin=>$listing) {
            $index++;
            echo "($index/$total) Processing $asin ...";
            if (empty($asin)) {
                echo " $asin is really empty\n";
                continue;
            }
            if (empty($listing)) {
                echo " $asin is empty\n";
                continue;
            }
            $mainListings = (empty($listing[$this->amazonConnector->mainCountry]) || !is_array($listing[$this->amazonConnector->mainCountry])) ? reset($listing) : $listing[$this->amazonConnector->mainCountry];
            if (!is_array($mainListings)) {
                echo " $asin is not an array\n";
                continue;
            }
            $mainListing = reset($mainListings);
            $variantProduct = VariantProduct::addUpdateVariant(
                variant: [
                    'imageUrl' => null,
                    'urlLink' => $this->amazonConnector->getUrlLink(AmazonConstants::amazonMerchant[$this->amazonConnector->mainCountry]['url']."/dp/$asin"),
                    'salePrice' => 0,
                    'saleCurrency' => '',
                    'title' => $this->getTitle($mainListing),
                    'attributes' => $this->getAttributes($mainListing),
                    'uniqueMarketplaceId' => $asin,
                    'apiResponseJson' => json_encode($listing),
                    'published' => true,
                ],
                importFlag: $importFlag,
                updateFlag: $updateFlag,
                marketplace: $this->amazonConnector->getMarketplace(),
                parent: $this->getFolder($asin),
            );
            $mainProduct = $variantProduct->getMainProduct();
            if ($mainProduct instanceof Product) {
                echo "Reg";
                Registry::setKey($asin, $mainProduct->getIwasku(), 'asin-to-iwasku');
            } else echo "NoReg";
            $skuRequired = empty($mainProduct) ? true : false;
            foreach ($listing as $country=>$countryListings) {
                if ($country === 'catalog') {
                    continue;
                }
                foreach ($countryListings as $countryListing) {
                    echo "$country ";
                    $this->processFieldCollection($variantProduct, $countryListing, $country);
                    if ($skuRequired) {
                        $sku = explode('_', $countryListing['seller-sku'] ?? '')[0] ?? '';
                        if ($this->checkIwasku($sku)) {
                            $mainProduct = Product::getByIwasku($sku, ['limit' => 1]);
                            if ($mainProduct instanceof Product) {
                                echo "Adding variant {$variantProduct->getId()} to {$mainProduct->getId()} ";
                                if ($mainProduct->addVariant($variantProduct)) {
                                    $skuRequired = false;
                                }
                            }
                        }
                    }
                }
            }
            echo "{$variantProduct->getId()} ";
            echo " OK\n";
        }
    }

    protected function processFieldCollection($variantProduct, $listing, $country)
    {
        $collection = $variantProduct->getAmazonMarketplace();
        $newCollection = new Fieldcollection();
        $found = false;
        $active = ($listing['status'] ?? '') === 'Active';
        foreach ($collection ?? [] as $amazonCollection) {
            if (!$amazonCollection instanceof AmazonMarketplace) {
                continue;
            }
            if ($amazonCollection->getListingId() === $listing['listing-id']) {
                $found = true;
                $amazonCollection->setMarketplaceId($country);
                $amazonCollection->setTitle($this->getTitle($listing));
                $amazonCollection->setUrlLink($this->amazonConnector->getUrlLink(AmazonConstants::amazonMerchant[$country]['url'].'/dp/' . ($listing['asin1'] ?? '')));
                $amazonCollection->setSalePrice($listing['price'] ?? 0);
                $amazonCollection->setSaleCurrency(AmazonConstants::getAmazonSaleCurrency($country));
                $amazonCollection->setSku($listing['seller-sku'] ?? '');
                $amazonCollection->setQuantity((int)($listing['quantity'] ?? 0)+0);
                $amazonCollection->setLastUpdate(Carbon::now());
                $amazonCollection->setMarketplace($this->amazonConnector->marketplace);
                $amazonCollection->setStatus($listing['status'] ?? '');
                $amazonCollection->setFulfillmentChannel($listing['fulfillment-channel'] ?? '');
            } else {
                if ($amazonCollection->getLastUpdate() === null || $amazonCollection->getLastUpdate() < Carbon::now()->subDays(3)) {
                    continue;
                }
            }
            if ($amazonCollection->getStatus() === 'Active') {
                $active = true;
            }
            $newCollection->add($amazonCollection);
        }
        if (!$found) {
            $amazonCollection = new AmazonMarketplace();
            $amazonCollection->setMarketplaceId($country);
            $amazonCollection->setLastUpdate(Carbon::now());
            $amazonCollection->setTitle($this->getTitle($listing));
            $amazonCollection->setUrlLink($this->amazonConnector->getUrlLink(AmazonConstants::amazonMerchant[$country]['url'].'/dp/' . ($listing['asin1'] ?? '')));
            $amazonCollection->setSalePrice($listing['price'] ?? 0);
            $amazonCollection->setSaleCurrency(AmazonConstants::getAmazonSaleCurrency($country));
            $amazonCollection->setSku($listing['seller-sku'] ?? '');
            $amazonCollection->setListingId($listing['listing-id'] ?? '');
            $amazonCollection->setMarketplace($this->amazonConnector->marketplace);
            $amazonCollection->setQuantity((int)($listing['quantity'] ?? 0)+0);
            $amazonCollection->setStatus($listing['status'] ?? '');
            $amazonCollection->setFulfillmentChannel($listing['fulfillment-channel'] ?? '');
            $newCollection->add($amazonCollection);
        }
        $variantProduct->setAmazonMarketplace($newCollection);
        if ($active) {
            $variantProduct->setPublished(true);
        } else {
            $variantProduct->setPublished(false);
            $variantProduct->setParent(Utility::checkSetPath('_Pasif', Utility::checkSetPath('Amazon', Utility::checkSetPath('Pazaryerleri'))));
        }
        $variantProduct->save();
    }
}