<?php

namespace App\Command;

use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Model\DataObject\Marketplace;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\EventListener\DataObjectListener;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
use App\Connector\Marketplace\ShopifyConnector;
use App\Connector\Marketplace\EtsyConnector;
use App\Connector\Marketplace\TrendyolConnector;
use App\Connector\Marketplace\BolConnector;
use App\Connector\Marketplace\EbayConnector;
use App\Connector\Marketplace\TakealotConnector;
use App\Connector\Marketplace\WallmartConnector;
use App\Connector\Marketplace\CiceksepetiConnector;
use App\Connector\Marketplace\HepsiburadaConnector;
use App\Connector\Marketplace\WayfairConnector;
use App\Connector\Marketplace\OzonConnector;
use App\Connector\IwabotConnector;



#[AsCommand(
    name: 'app:import',
    description: 'Imports products from Marketplaces!'
)]
class ImportCommand extends AbstractCommand
{
    private static bool $downloadFlag = false;
    private static bool $importFlag = false;
    private static bool $updateFlag = false;
    private static bool $ordersFlag = false;
    private static bool $inventoryFlag = false;
    private static array $marketplaceArg = [];
    private static bool $amazonFlag = false;
    private static bool $etsyFlag = false;
    private static bool $shopifyFlag = false;
    private static bool $trendyolFlag = false;
    private static bool $allFlag = false;
    private static bool $bolcomFlag = false;
    private static bool $ebayFlag = false;
    private static bool $takealotFlag = false;
    private static bool $wallmartFlag = false;
    private static bool $ciceksepetiFlag = false;
    private static bool $hepsiburadaFlag = false;
    private static bool $wayfairFlag = false;
    private static bool $ozonFlag = false;

    private EventDispatcherInterface $eventDispatcher;
    private DataObjectListener $dataObjectListener;

    public function __construct(EventDispatcherInterface $eventDispatcher, DataObjectListener $dataObjectListener)
    {
        parent::__construct();
        $this->eventDispatcher = $eventDispatcher;
        $this->dataObjectListener = $dataObjectListener;
    }

    protected function configure(): void
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
            ->addOption('wallmart', null, InputOption::VALUE_NONE, 'If set, processes Wallmart objects.')
            ->addOption('ciceksepeti', null, InputOption::VALUE_NONE, 'If set, processes ciceksepeti objects.')
            ->addOption('hepsiburada', null, InputOption::VALUE_NONE, 'If set, processes hepsiburada objects.')
            ->addOption('wayfair', null, InputOption::VALUE_NONE, 'If set, processes wayfair objects.')
            ->addOption('ozon', null, InputOption::VALUE_NONE, 'If set, processes ozon objects.')
            ->addOption('list', null, InputOption::VALUE_NONE, 'Lists all possible objects for processing.')
            ->addOption('download', null, InputOption::VALUE_NONE, 'Downloads listing data from the specified marketplace.')
            ->addOption('import', null, InputOption::VALUE_NONE, 'Imports downloaded listing data to create missing objects in the specified marketplace.')
            ->addOption('update', null, InputOption::VALUE_NONE, 'Updates existing objects with the downloaded data in the specified marketplace.')
            ->addOption('orders', null, InputOption::VALUE_NONE, 'Downloads orders from the specified marketplace.')
            ->addOption('inventory', null, InputOption::VALUE_NONE, 'Downloads inventory data from the specified marketplace.')
            ->addOption('iwabot', null, InputOption::VALUE_NONE, 'Downloads inventory from iwabot/USA warehouse')
            ->addOption('test', null, InputOption::VALUE_NONE, 'Test command.')
            ->addOption('memory-table', null, InputOption::VALUE_NONE, 'Populates the in-memory table for Shopify line items.');
    }

    private static function getMarketplaceObjects(): array
    {
        return Marketplace::getMarketplaceList();
    }

    private function removeListeners(): void
    {
        $this->eventDispatcher->removeSubscriber($this->dataObjectListener);
    }

    private function addListeners(): void
    {
        $this->eventDispatcher->addSubscriber($this->dataObjectListener);
    }

    private static function listMarketplaces(): int
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
        self::$wallmartFlag = $input->getOption('wallmart');
        self::$ciceksepetiFlag = $input->getOption('ciceksepeti');
        self::$hepsiburadaFlag = $input->getOption('hepsiburada');
        self::$wayfairFlag = $input->getOption('wayfair');
        self::$ozonFlag = $input->getOption('ozon');
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

            if ($input->getOption('iwabot')) {
                IwabotConnector::downloadReport();
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
                        if (!self::$wallmartFlag && $marketplace->getMarketplaceType() === 'Wallmart') {
                            continue;
                        }
                        if (!self::$ciceksepetiFlag && $marketplace->getMarketplaceType() === 'Ciceksepeti') {
                            continue;
                        }
                        if (!self::$hepsiburadaFlag && $marketplace->getMarketplaceType() === 'Hepsiburada') {
                            continue;
                        }
                        if (!self::$wayfairFlag && $marketplace->getMarketplaceType() === 'Wayfair') {
                            continue;
                        }
                        if (!self::$ozonFlag && $marketplace->getMarketplaceType() === 'Ozon') {
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
                    'Wallmart' => new WallmartConnector($marketplace),
                    'Ciceksepeti' => new CiceksepetiConnector($marketplace),
                    'Hepsiburada' => new HepsiburadaConnector($marketplace),
                    'Wayfair' => new WayfairConnector($marketplace),
                    'Ozon' => new OzonConnector($marketplace),
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
        } catch (Exception|\Exception) {
        } finally {
            $this->addListeners();
        }
        return Command::SUCCESS;
    }
}