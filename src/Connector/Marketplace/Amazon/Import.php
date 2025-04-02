<?php

namespace App\Connector\Marketplace\Amazon;

use Doctrine\DBAL\Exception;
use Pimcore\Db;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject\Fieldcollection\Data\AmazonMarketplace;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Folder;

use Carbon\Carbon;

use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use App\Utils\Utility;
use App\Utils\Registry;
use Pimcore\Model\Element\DuplicateFullPathException;

class Import
{
    public Connector $connector;

    public array $iwaskuList = [];

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    
    private function getAttributes($listing): string
    {
        $title = $listing['item-name'];
        if (preg_match('/\(([^()]*)\)[^(]*$/', $title, $matches)) {
            return trim($matches[1]);
        }
        return '';    
    }

    private function getTitle($listing): string
    {
        return trim(str_replace('('.$this->getAttributes($listing).')','',$listing['item-name'] ?? ''));
    }

    /**
     * @throws Exception|DuplicateFullPathException
     */
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

    /**
     * @throws Exception
     */
    protected function checkIwasku($iwasku): bool
    {
        if (empty($this->iwaskuList)) {
            $db = Db::get();
            $this->iwaskuList = $db->fetchFirstColumn("SELECT DISTINCT iwasku FROM object_store_product WHERE iwasku IS NOT NULL ORDER BY iwasku");
            $this->iwaskuList = array_filter( $this->iwaskuList);
        }
        return in_array($iwasku, $this->iwaskuList);
    }

    /**
     * @throws Exception|DuplicateFullPathException|\Exception
     */
    public function import($updateFlag, $importFlag): void
    {
        $total = count($this->connector->listings);
        $index = 0;
        foreach ($this->connector->listings as $asin=>$listing) {
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
            $mainListings = (empty($listing[$this->connector->mainCountry]) || !is_array($listing[$this->connector->mainCountry])) ? reset($listing) : $listing[$this->connector->mainCountry];
            if (!is_array($mainListings)) {
                echo " $asin is not an array\n";
                continue;
            }
            $mainListing = reset($mainListings);
            $ean = trim($mainListing['product-id'] ?? '');
            foreach ($listing as $countryListings) {
                foreach ($countryListings as $countryListing) {
                    if (str_starts_with($ean, '868408')) {
                        break 2;
                    } else {
                        $ean = trim($countryListing['product-id'] ?? '');
                    }
                }
            }
            if (str_starts_with($ean, '868408')) {
                $ean = trim($ean);
            } else {
                $ean = '';
            }
            $variantProduct = VariantProduct::addUpdateVariant(
                variant: [
                    'imageUrl' => null,
                    'urlLink' => $this->connector->getUrlLink(AmazonConstants::amazonMerchant[$this->connector->mainCountry]['url']."/dp/$asin"),
                    'salePrice' => 0,
                    'saleCurrency' => '',
                    'title' => $this->getTitle($mainListing),
                    'attributes' => $this->getAttributes($mainListing),
                    'uniqueMarketplaceId' => $asin,
                    'apiResponseJson' => json_encode($listing),
                    'published' => true,
                    'ean' => $ean,
                ],
                importFlag: $importFlag,
                updateFlag: $updateFlag,
                marketplace: $this->connector->getMarketplace(),
                parent: $this->getFolder($asin),
            );
            $mainProduct = $variantProduct->getMainProduct();
            $skuRequired = empty($mainProduct);
            $mainProduct = is_array($mainProduct) ? reset($mainProduct) : $mainProduct;
            if ($mainProduct instanceof Product) {
                echo "Reg ";
                Registry::setKey($asin, $mainProduct->getIwasku(), 'asin-to-iwasku');
            } else {
                echo "NoReg ";
            }
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
                            $mainProduct = Product::getByIwasku($sku, 1);
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
            $variantProduct->save();
            echo "{$variantProduct->getId()} ";
            echo " OK\n";
        }
    }

    /**
     * @throws DuplicateFullPathException
     */
    protected function processFieldCollection($variantProduct, $listing, $country): void
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
                $this->setAmazonCollectionProperties($amazonCollection, $listing, $country, $this->connector->marketplace);
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
            $this->setAmazonCollectionProperties($amazonCollection, $listing, $country, $this->connector->marketplace);
            $amazonCollection->setListingId($listing['listing-id'] ?? '');
            $newCollection->add($amazonCollection);
        }
        $variantProduct->setAmazonMarketplace($newCollection);
        if ($active) {
            $variantProduct->setPublished(true);
        } else {
            $variantProduct->setPublished(false);
            $variantProduct->setParent(Utility::checkSetPath('_Pasif', Utility::checkSetPath('Amazon', Utility::checkSetPath('Pazaryerleri'))));
        }
    }

    /**
     * @param AmazonMarketplace $amazonCollection
     * @param $listing
     * @param $country
     * @param $marketplace
     * @return void
     */
    protected function setAmazonCollectionProperties(AmazonMarketplace $amazonCollection, $listing, $country, $marketplace): void
    {
        print_r(json_encode($listing));
        $amazonCollection->setLastUpdate(Carbon::now());
        $amazonCollection->setMarketplaceId($country);
        $amazonCollection->setTitle($this->getTitle($listing));
        $amazonCollection->setUrlLink($this->connector->getUrlLink(AmazonConstants::amazonMerchant[$country]['url'] . '/dp/' . ($listing['asin1'] ?? '')));
        $amazonCollection->setSalePrice($listing['price'] ?? 0);
        $amazonCollection->setStatus($listing['status'] ?? '');
        $amazonCollection->setQuantity((int)($listing['quantity'] ?? 0));
        $amazonCollection->setSaleCurrency(AmazonConstants::getAmazonSaleCurrency($country));
        $amazonCollection->setFulfillmentChannel($listing['fulfillment-channel'] ?? $listing['fulfilment-channel'] ?? '');
        $amazonCollection->setMarketplace($marketplace);
        $amazonCollection->setSku($listing['seller-sku'] ?? '');
        $ean = trim($listing['product-id'] ?? '');
        if (str_starts_with($ean, '868408')) {
            $amazonCollection->setEan($ean);
        }
    }
}