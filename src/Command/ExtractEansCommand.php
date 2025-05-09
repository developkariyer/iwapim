<?php

namespace App\Command;

use App\Controller\ShopifyController;
use App\Utils\Registry;
use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:ean',
    description: 'Extract Ean/Gtins!'
)]
class ExtractEansCommand extends AbstractCommand
{
    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $db = Db::get();
        $listingList = $db->fetchAllAssociative(ShopifyController::marketplaceListingsSql);

        $counter = [];
        $totalCount = count($listingList);
        $index = 0;

        foreach ($listingList as $listing) {
            $index++;
            echo "$index/$totalCount - "; foreach ($counter as $key => $value) echo "$key:$value ";

            $ean = match($listing['marketplaceType']) {
                'Shopify' => $this->eanFromShopify($listing),
                'Amazon' => $this->eanFromAmazon($listing),
                'Bol.com' => $this->eanFromBolcom($listing),
                default => ''
            };

            if (!empty($ean)) {
                $counter[$listing['marketplaceType']] = ($counter[$listing['marketplaceType']] ?? 0) + 1;
                echo $ean;
                Registry::setKey($listing['id'], $ean, 'listing-to-ean');
            }
            echo "          \r";
        }
        echo "\n";

        // Return success status code
        return Command::SUCCESS;
    }

    /**
     * @throws Exception
     */
    private function eanFromShopify($listing)
    {
        $json = json_decode($this->readApiJson($listing['id']), true);
        if (empty($json)) {
            return '';
        }
        return $json['barcode'] ?? '';
    }

    /**
     * @throws Exception
     */
    private function eanFromAmazon($listing)
    {
        $asin = $listing['uniqueMarketplaceId'];
        $jsons = $this->readAsinJson($asin);
        foreach ($jsons as $jsonraw) {
            $json = json_decode($jsonraw, true);
            if (empty($json)) {
                continue;
            }
            foreach ($json['identifiers'] ?? [] as $marketplace) {
                foreach ($marketplace['identifiers'] ?? [] as $identifier) {
                    if ($identifier['identifierType'] === 'EAN') {
                        return $identifier['identifier'] ?? '';
                    }
                }
            }
        }
        return '';
    }

    /**
     * @throws Exception
     */
    private function readAsinJson($asin): array
    {
        $db = Db::get();
        return $db->fetchFirstColumn("SELECT json_data FROM iwa_json_store WHERE field_name = ?", [$asin]);
    }

    /**
     * @throws Exception
     */
    private function readApiJson($id)
    {
        return $this->readJson($id, 'apiResponseJson');

    }

    /**
     * @throws Exception
     */
    private function readParentJson($id)
    {
        return $this->readJson($id, 'parentResponseJson');
    }

    /**
     * @throws Exception
     */
    private function readJson($id, $fieldName)
    {
        $db = Db::get();
        return $db->fetchOne("SELECT json_data FROM iwa_json_store WHERE object_id = ? AND field_name = ?", [$id, $fieldName]);
    }

    /**
     * @throws Exception
     */
    private function eanFromBolcom(array $listing)
    {
        $json = json_decode($this->readApiJson($listing['id']), true);
        if (empty($json)) {
            return '';
        }
        return $json['ean'] ?? '';
    }
}
