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

        foreach ($listingList as $listing) {
            echo $listing['id'] . PHP_EOL;
            $ean = match($listing['marketplaceType']) {
                'Shopify' => $this->eanFromShopify($listing),
                default => ''
            };
            if (!empty($ean)) {
                Registry::setKey($listing['id'], $ean, 'listing-to-ean');
            }
        }

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
}
