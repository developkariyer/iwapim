<?php

namespace App\Command;

use App\Connector\IwabotConnector;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
use App\Connector\Marketplace\BolConnector;
use App\Connector\Marketplace\CiceksepetiConnector;
use App\Connector\Marketplace\EbayConnector;
use App\Connector\Marketplace\EtsyConnector;
use App\Connector\Marketplace\HepsiburadaConnector;
use App\Connector\Marketplace\Ozon\Connector as OzonConnector;
use App\Connector\Marketplace\ShopifyConnector;
use App\Connector\Marketplace\TakealotConnector;
use App\Connector\Marketplace\TrendyolConnector;
use App\Connector\Marketplace\WallmartConnector;
use App\Connector\Marketplace\WayfairConnector;
use App\EventListener\DataObjectListener;
use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject\Marketplace;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


#[AsCommand(
    name: 'app:import',
    description: 'Imports products from Marketplaces!'
)]
class ImportCommand extends AbstractCommand
{
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

    private function removeListeners(): void
    {
        $this->eventDispatcher->removeSubscriber($this->dataObjectListener);
    }

    private function addListeners(): void
    {
        $this->eventDispatcher->addSubscriber($this->dataObjectListener);
    }

    private static function listMarketplaces($marketplaces): int
    {
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

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $downloadFlag = $input->getOption('download');
        $importFlag = $input->getOption('import');
        $updateFlag = $input->getOption('update');
        $ordersFlag = $input->getOption('orders');
        $inventoryFlag = $input->getOption('inventory');
        $marketplaceArg = $input->getArgument('marketplace');
        $amazonFlag = $input->getOption('amazon');
        $etsyFlag = $input->getOption('etsy');
        $shopifyFlag = $input->getOption('shopify');
        $trendyolFlag = $input->getOption('trendyol');
        $bolcomFlag = $input->getOption('bolcom');
        $ebayFlag = $input->getOption('ebay');
        $takealotFlag = $input->getOption('takealot');
        $wallmartFlag = $input->getOption('wallmart');
        $ciceksepetiFlag = $input->getOption('ciceksepeti');
        $hepsiburadaFlag = $input->getOption('hepsiburada');
        $wayfairFlag = $input->getOption('wayfair');
        $ozonFlag = $input->getOption('ozon');
        $allFlag = $input->getOption('all');

        $this->removeListeners();

        try {
            if ($input->getOption('test')) {
                $marketplace = Marketplace::getById(199128);
                $connector = new BolConnector($marketplace);
                $connector->downloadOfferReport();
                return Command::SUCCESS;
            }

            if ($input->getOption('iwabot')) {
                IwabotConnector::downloadReport();
            }

            $marketplaces = Marketplace::getMarketplaceList();

            if ($input->getOption('list')) {
                return self::listMarketplaces($marketplaces);
            }

            foreach ($marketplaces as $marketplace) {
                if (!$allFlag) {
                    if (!empty($marketplaceArg)) {
                        if (!in_array($marketplace->getKey(), $marketplaceArg)) {
                            continue;
                        }
                    } else {
                        if (!$amazonFlag && $marketplace->getMarketplaceType() === 'Amazon') {
                            continue;
                        }
                        if (!$etsyFlag && $marketplace->getMarketplaceType() === 'Etsy') {
                            continue;
                        }
                        if (!$shopifyFlag && $marketplace->getMarketplaceType() === 'Shopify') {
                            continue;
                        }
                        if (!$trendyolFlag && $marketplace->getMarketplaceType() === 'Trendyol') {
                            continue;
                        }
                        if (!$bolcomFlag && $marketplace->getMarketplaceType() === 'Bol.com') {
                            continue;
                        }
                        if (!$ebayFlag && $marketplace->getMarketplaceType() === 'Ebay') {
                            continue;
                        }
                        if (!$takealotFlag && $marketplace->getMarketplaceType() === 'Takealot') {
                            continue;
                        }
                        if (!$wallmartFlag && $marketplace->getMarketplaceType() === 'Wallmart') {
                            continue;
                        }
                        if (!$ciceksepetiFlag && $marketplace->getMarketplaceType() === 'Ciceksepeti') {
                            continue;
                        }
                        if (!$hepsiburadaFlag && $marketplace->getMarketplaceType() === 'Hepsiburada') {
                            continue;
                        }
                        if (!$wayfairFlag && $marketplace->getMarketplaceType() === 'Wayfair') {
                            continue;
                        }
                        if (!$ozonFlag && $marketplace->getMarketplaceType() === 'Ozon') {
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
                $connector->download($downloadFlag);
                echo "done.\n";
                if ($updateFlag || $importFlag) {
                    echo "    Importing...";
                    $connector->import($updateFlag, $importFlag);
                    echo "done.\n";
                }
                if ($ordersFlag) {
                    echo "    Getting orders... ";
                    $connector->downloadOrders();
                }
                if ($inventoryFlag) {
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