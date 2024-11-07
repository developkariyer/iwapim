<?php

namespace App\Command;

use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Model\DataObject\Marketplace;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\EventListener\DataObjectListener;
use App\Connector\Marketplace\AmazonConnector;
use App\Connector\Marketplace\ShopifyConnector;
use App\Connector\Marketplace\EtsyConnector;
use App\Connector\Marketplace\TrendyolConnector;
use App\Connector\Marketplace\BolConnector;
use App\Connector\Marketplace\EbayConnector;
use App\Connector\Marketplace\TakealotConnector;



#[AsCommand(
    name: 'app:import',
    description: 'Imports products from Marketplaces!'
)]
class ImportCommand extends AbstractCommand
{
    private static $downloadFlag = false;
    private static $importFlag = false;
    private static $updateFlag = false;
    private static $ordersFlag = false;
    private static $inventoryFlag = false;
    private static $resetVariantsFlag = null;
    private static $marketplaceArg = null;
    private static $amazonFlag = false;
    private static $etsyFlag = false;
    private static $shopifyFlag = false;
    private static $trendyolFlag = false;
    private static $allFlag = false;
    private static $bolcomFlag = false;
    private static $ebayFlag = false;
    private static $takealotFlag = false;
    private static $itemCodes = [];

    private EventDispatcherInterface $eventDispatcher;
    private DataObjectListener $dataObjectListener;

    public function __construct(EventDispatcherInterface $eventDispatcher, DataObjectListener $dataObjectListener)
    {
        parent::__construct();
        $this->eventDispatcher = $eventDispatcher;
        $this->dataObjectListener = $dataObjectListener;
    }

    protected function configure()
    {
        $this
            ->addArgument('marketplace', InputOption::VALUE_OPTIONAL, 'Specify the marketplace to import from. Leave empty to process all available marketplaces.')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Processes all objects across all marketplaces.')
            ->addOption('amazon', null, InputOption::VALUE_NONE, 'If set, processes Amazon objects.')
            ->addOption('etsy', null, InputOption::VALUE_NONE, 'If set, processes Etsy objects.')
            ->addOption('shopify', null, InputOption::VALUE_NONE, 'If set, processes Shopify objects.')
            ->addOption('trendyol', null, InputOption::VALUE_NONE, 'If set, processes Trendyol objects.')
            ->addOption('bolcom', null, InputOption::VALUE_NONE, 'If set, processes Bol.com objects.')
            ->addOption('ebay', null, InputOption::VALUE_NONE, 'If set, processes Ebay objects.')
            ->addOption('takealot', null, InputOption::VALUE_NONE, 'If set, processes Takealot objects.')
            ->addOption('list', null, InputOption::VALUE_NONE, 'Lists all possible objects for processing.')
            ->addOption('download', null, InputOption::VALUE_NONE, 'Downloads listing data from the specified marketplace.')
            ->addOption('import', null, InputOption::VALUE_NONE, 'Imports downloaded listing data to create missing objects in the specified marketplace.')
            ->addOption('update', null, InputOption::VALUE_NONE, 'Updates existing objects with the downloaded data in the specified marketplace.')
            ->addOption('orders', null, InputOption::VALUE_NONE, 'Downloads orders from the specified marketplace.')
            ->addOption('inventory', null, InputOption::VALUE_NONE, 'Downloads inventory data from the specified marketplace.')
            ->addOption('test', null, InputOption::VALUE_NONE, 'Test command.')
            ->addOption('memory-table', null, InputOption::VALUE_NONE, 'Populates the in-memory table for Shopify line items.');
    }
    
    

    private static function getMarketplaceObjects($type = null): array
    {
        return Marketplace::getMarketplaceList($type);
    }

    private function removeListeners()
    {
        $this->eventDispatcher->removeSubscriber($this->dataObjectListener);
    }

    private function addListeners()
    {
        $this->eventDispatcher->addSubscriber($this->dataObjectListener);
    }

    private static function listMarketplaces()
    {
        $marketplaces = self::getMarketplaceObjects();
        $mp = [];
        foreach ($marketplaces as $marketplace) {
            if (!isset($mp[$marketplace->getMarketplaceType()])) {
                $mp[$marketplace->getMarketplaceType()] = [];
            }
            $mp[$marketplace->getMarketplaceType()][] = "{$marketplace->getKey()}  ({$marketplace->getMarketplaceUrl()})";
        }
        foreach ($mp as $type => $keys) {
            echo "{$type}:\n";
            foreach ($keys as $key) {
                echo "    {$key}\n";
            }
        }
        return Command::SUCCESS;
    }

    protected static function prepareOrderTable()
    {
        $uniqueMarketplaceId = '';
        $variantObject = VariantProduct::findOneByField('uniqueMarketplaceId', $uniqueMarketplaceId);
        $marketplace = $variantObject->getMarketplace();
        $marketplaceKey = $marketplace->getKey(); // field 1
        $mainProductObjectArray = $variantObject->getMainProduct(); // [] veya null
        $mainProductObject = reset($mainProductObjectArray);
        $productCode = $mainProductObject->getProductCode(); //field 2
        if ($mainProductObject->level() == 1) {
            $parent = $mainProductObject->getParent();
            $parentProductCode = $parent->getProductCode(); // field 3
        } else {
            $parentProductCode = $productCode;
        }
        $productIdentifier = $mainProductObject->getProductIdentifier();
        $productType = strtok($productIdentifier,'-'); // field 4
        // TODO:  verinin normalleştirilmesi: döviz kurları
        // WARNING: para için asla float kullanma
        // - bcmath fonksiyonlarını kullan
        // - veritabanında decimal kullan
        // - önce 100 ile çarp, işlemini yap, round et, 100'e böl
    }

    protected static function prepareShopifyLineItems()
    {
        $db = \Pimcore\Db::get();
        echo "Truncating in-memory Shopify line_items table\n";
        $db->query("DELETE FROM iwa_shopify_orders_line_items;");
        echo "Populating in-memory Shopify line_items table\n";
        $db->query("INSERT INTO iwa_shopify_orders_line_items (created_at, order_id, variant_id, quantity)
            SELECT 
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.created_at')) AS created_at,
                JSON_UNQUOTE(JSON_EXTRACT(json, '$.id')) AS order_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) AS variant_id,
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.quantity')) AS quantity
            FROM 
                iwa_shopify_orders
                CROSS JOIN JSON_TABLE(json, '$.line_items[*]' COLUMNS (
                    value JSON PATH '$'
                )) AS line_item
            WHERE 
                JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) IS NOT NULL
                AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) != 'null'
                AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) != ''
                AND CAST(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) AS UNSIGNED) > 0;"
        );
        return Command::SUCCESS;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        self::$downloadFlag = $input->getOption('download');
        self::$importFlag = $input->getOption('import');
        self::$updateFlag = $input->getOption('update');
        self::$ordersFlag = $input->getOption('orders');
        self::$inventoryFlag = $input->getOption('inventory');
        self::$marketplaceArg = $input->getArgument('marketplace');
        self::$amazonFlag = $input->getOption('amazon');
        self::$etsyFlag = $input->getOption('etsy');
        self::$shopifyFlag = $input->getOption('shopify');
        self::$trendyolFlag = $input->getOption('trendyol');
        self::$bolcomFlag = $input->getOption('bolcom');
        self::$ebayFlag = $input->getOption('ebay');
        self::$takealotFlag = $input->getOption('takealot');
        self::$allFlag = $input->getOption('all');

        $this->removeListeners();

        try {
            if ($input->getOption('test')) {
                $marketplace = Marketplace::getById(199128);
                $connector = new BolConnector($marketplace);
                $connector->downloadOfferReport();
                return Command::SUCCESS;
            }
            if ($input->getOption('list')) {
                return self::listMarketplaces();
            }

            if ($input->getOption('memory-table')) {
                return self::prepareShopifyLineItems();
            }

            $marketplaces = self::getMarketplaceObjects();
            foreach ($marketplaces as $marketplace) {
                if (!self::$allFlag) {
                    if (!empty(self::$marketplaceArg)) {
                        if (!in_array($marketplace->getKey(), self::$marketplaceArg)) {
                            continue;
                        }
                    } else {
                        if (!self::$amazonFlag && $marketplace->getMarketplaceType() === 'Amazon') {
                            continue;
                        }
                        if (!self::$etsyFlag && $marketplace->getMarketplaceType() === 'Etsy') {
                            continue;
                        }
                        if (!self::$shopifyFlag && $marketplace->getMarketplaceType() === 'Shopify') {
                            continue;
                        }
                        if (!self::$trendyolFlag && $marketplace->getMarketplaceType() === 'Trendyol') {
                            continue;
                        }
                        if (!self::$bolcomFlag && $marketplace->getMarketplaceType() === 'Bol.com') {
                            continue;
                        }
                        if (!self::$ebayFlag && $marketplace->getMarketplaceType() === 'Ebay') {
                            continue;
                        }
                        if (!self::$takealotFlag && $marketplace->getMarketplaceType() === 'Takealot') {
                            continue;
                        }
                    }
                }
                
                echo "Processing {$marketplace->getMarketplaceType()} Marketplace {$marketplace->getKey()} ...\n";
                $connector = match ($marketplace->getMarketplaceType()) {
                    'Amazon' => new AmazonConnector($marketplace),
                    'Etsy' => new EtsyConnector($marketplace),
                    'Shopify' => new ShopifyConnector($marketplace),
                    'Trendyol' => new TrendyolConnector($marketplace),
                    'Bol.com' => new BolConnector($marketplace),
                    'Ebay' => new EbayConnector($marketplace),
                    'Takealot' => new TakealotConnector($marketplace),
                    default => null,
                };
                if (!$connector) {
                    echo "No connector available, skipping...\n";
                    continue;
                }
                echo "    Downloading... ";
                $connector->download(self::$downloadFlag);
                echo "done.\n";
                if (self::$updateFlag || self::$importFlag) {
                    echo "    Importing...";
                    $connector->import(self::$updateFlag, self::$importFlag);
                    echo "done.\n";
                }
                if (self::$ordersFlag) {
                    echo "    Getting orders... ";
                    $connector->downloadOrders();
                }
                if (self::$inventoryFlag) {
                    echo "    Getting inventory... ";
                    $connector->downloadInventory();
                }
                echo "done.\n";
            }
        } finally {
            $this->addListeners();
        }
        return Command::SUCCESS;
    }
}