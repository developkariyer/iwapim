<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(
    name: 'app:identifiers',
    description: 'Sync Identifiers!'
)]
class IdentifiersCommand extends AbstractCommand
{
    
    protected function configure(): void
    {
        $this
            ->addArgument('marketplace', InputOption::VALUE_OPTIONAL, 'The marketplace to import from.')
            ->addOption('download', null, InputOption::VALUE_NONE, 'If set, Shopify listing data will always be downloaded.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
/*        self::$downloadFlag = $input->getOption('download');
        self::$marketplaceArg = $input->getArgument('marketplace');*/
        
        self::processAmazon();
        self::processShopify();
        self::processEtsy();
        
        return Command::SUCCESS;
    }

    protected static function processShopify()
    {
        echo "Loading Shopify objects...\n";
        $listObject = new ShopifyVariantListing();
        $listObject->setUnpublished(true);
        $shopifyList = $listObject->load();

        $db = \Pimcore\Db::get();
        $db->beginTransaction();
        $stmt = $db->prepare('INSERT IGNORE INTO iwa_identifiers (object_id, identifier_type, identifier) VALUES (?, ?, ?)');

        foreach ($shopifyList as $variant) {
            echo "    Processing {$variant->getId()}...";
            if (!empty($variant->getSku())) {
                $stmt->execute([$variant->getId(), 'sku', $variant->getSku()]);
                echo " sku ({$variant->getSku()})";
            }
            if (!empty($variant->getBarcode())) {
                $stmt->execute([$variant->getId(), 'ean', $variant->getBarcode()]);
                echo " ean ({$variant->getBarcode()})";
            }
            echo "\n";
        }
        $db->commit();
    }

    protected static function processEtsy()
    {
        echo "Loading Etsy objects...\n";
        $listObject = new EtsyVariantListing();
        $listObject->setUnpublished(true);
        $etsyList = $listObject->load();

        $db = \Pimcore\Db::get();
        $db->beginTransaction();
        $stmt = $db->prepare('INSERT IGNORE INTO iwa_identifiers (object_id, identifier_type, identifier) VALUES (?, ?, ?)');

        foreach ($etsyList as $variant) {
            echo "    Processing {$variant->getId()}...";
            if (!empty($variant->getSku())) {
                $stmt->execute([$variant->getId(), 'sku', $variant->getSku()]);
                echo " sku ({$variant->getSku()})";
            }
            echo "\n";
        }
        $db->commit();
    }

    protected static function processAmazon()
    {
        echo "Loading Amazon objects...\n";
        $listObject = new AmazonVariantListing();
        $listObject->setUnpublished(true);
        $amazonList = $listObject->load();

        $db = \Pimcore\Db::get();
        $db->beginTransaction();
        $stmt = $db->prepare('INSERT IGNORE INTO iwa_identifiers (object_id, identifier_type, identifier) VALUES (?, ?, ?)');

        $cvs = [];

        foreach ($amazonList as $amazonListing) {
            echo "    Processing {$amazonListing->getId()}...";
            $identifiers = self::getAmazonIdentifiers($amazonListing);
            $cvs[] = array_merge($identifiers, ['iwapim_id' => [$amazonListing->getId()]]);
            $objectId = $amazonListing->getId();
            foreach ($identifiers as $type=>$values) {
                echo " $type (".implode(', ', $values).")";
                foreach ($values as $value) {
                    $stmt->execute([$objectId, $type, $value]);
                }
            }
            echo "\n";
        }
        $db->commit();

        $fileoutput = implode("\t", array_keys($cvs[0]))."\n";
        foreach ($cvs as $row) {
            foreach ($row as $value) {
                $fileoutput .= implode(",", $value)."\t";
            }
            $fileoutput .= "\n";
        }
        file_put_contents('/var/www/iwapim/tmp/amazon_identifiers.tsv', $fileoutput);
    }

    protected static function extractSummary($summary, &$identifiers) {
        $json = json_decode($summary, true);
        foreach ($json as $item) {
            if (isset($item['asin'])) $identifiers['asin'][] = $item['asin'];
            if (isset($item['fnSku'])) $identifiers['fnsku'][] = $item['fnSku'];
        }
    }

    protected static function extractAttributes($attributes, &$identifiers) {
        $json = json_decode($attributes, true);
        foreach ($json as $key=>$value) {
            if ($key === 'externally_assigned_product_identifier') {
                foreach ($value as $item) {
                    if (isset($item['type']) && isset($item['value'])) $identifiers[$item['type']][] = $item['value'];
                }
            }
            if ($key === 'part_number') {
                foreach ($value as $item) {
                    if (isset($item['value'])) $identifiers['mpn'][] = $item['value'];
                }
            }
        }
    }

    protected static function extractOffers($offers, &$identifiers) {
        $json = json_decode($offers, true);
        foreach ($json as $item) {
            foreach ($item as $key=>$value) {
                if ($key === 'price') {
                    if (isset($value['amount']) && isset($value['currency'])) $identifiers['offers'][] = "{$value['amount']} {$value['currency']}";
                }
            }
        }
    }

    protected static function getAmazonIdentifiers(AmazonVariant $amazonListing): array
    {
        $identifiers = [
            'asin' => [],
            'ean' => [],
            'sku' => [$amazonListing->getSku()],
            'upc' => [],
            'mpn' => [],
            'gtin' => [],
            'fnsku' => [],
        ];

        self::extractSummary($amazonListing->getSummaries(), $identifiers);
        self::extractAttributes($amazonListing->getAttributes(), $identifiers);
//        self::extractOffers($amazonListing->getOffers(), $identifiers);

        return $identifiers;
    }

}
