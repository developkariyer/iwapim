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
use Pimcore\Model\Notification\Service\NotificationService;
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
    private NotificationService $notificationService;

    public function __construct(EventDispatcherInterface $eventDispatcher, DataObjectListener $dataObjectListener, NotificationService $notificationService)
    {
        parent::__construct();
        $this->eventDispatcher = $eventDispatcher;
        $this->dataObjectListener = $dataObjectListener;
        $this->notificationService = $notificationService;
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
            ->addOption('iwabot', null, InputOption::VALUE_NONE, 'Downloads inventory from iwabot/USA warehouse')

            ->addOption('list', null, InputOption::VALUE_NONE, 'Lists all possible objects for processing.')
            ->addOption('download', null, InputOption::VALUE_NONE, 'Downloads listing data from the specified marketplace.')
            ->addOption('import', null, InputOption::VALUE_NONE, 'Imports downloaded listing data to create missing objects in the specified marketplace.')
            ->addOption('update', null, InputOption::VALUE_NONE, 'Updates existing objects with the downloaded data in the specified marketplace.')
            ->addOption('orders', null, InputOption::VALUE_NONE, 'Downloads orders from the specified marketplace.')
            ->addOption('inventory', null, InputOption::VALUE_NONE, 'Downloads inventory data from the specified marketplace.')
            ->addOption('attributes', null, InputOption::VALUE_NONE, 'Download marketplace information required for making listings.')
        ;
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
            $marketplaceType = $marketplace->getMarketplaceType();
            if (!isset($mp[$marketplaceType])) {
                $mp[$marketplaceType] = [];
            }
            $mp[$marketplaceType][] = "{$marketplace->getKey()}  ({$marketplace->getMarketplaceUrl()})";
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
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $downloadFlag = $input->getOption('download');
        $importFlag = $input->getOption('import');
        $updateFlag = $input->getOption('update');
        $ordersFlag = $input->getOption('orders');
        $inventoryFlag = $input->getOption('inventory');
        $attributesFlag = $input->getOption('attributes');

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

        $notificationMessage = "Import report for ".date("Y-m-d H:i:s").":\n";
        $notificationSendFlag = false;

        try {
            if ($input->getOption('iwabot')) {
                IwabotConnector::downloadReport();
                $notificationMessage .= "IWA USA Warehouse inventory downloaded\n";
            }

            $marketplaces = Marketplace::getMarketplaceList();

            if ($input->getOption('list')) {
                return self::listMarketplaces($marketplaces);
            }

            foreach ($marketplaces as $marketplace) {
                $marketplaceType = $marketplace->getMarketplaceType();
                if (!$allFlag) {
                    if (!empty($marketplaceArg)) {
                        if (!in_array($marketplace->getKey(), $marketplaceArg)) {
                            continue;
                        }
                    } else {
                        if (!$amazonFlag && $marketplaceType === 'Amazon') {
                            continue;
                        }
                        if (!$etsyFlag && $marketplaceType === 'Etsy') {
                            continue;
                        }
                        if (!$shopifyFlag && $marketplaceType === 'Shopify') {
                            continue;
                        }
                        if (!$trendyolFlag && $marketplaceType === 'Trendyol') {
                            continue;
                        }
                        if (!$bolcomFlag && $marketplaceType === 'Bol.com') {
                            continue;
                        }
                        if (!$ebayFlag && $marketplaceType === 'Ebay') {
                            continue;
                        }
                        if (!$takealotFlag && $marketplaceType === 'Takealot') {
                            continue;
                        }
                        if (!$wallmartFlag && $marketplaceType === 'Wallmart') {
                            continue;
                        }
                        if (!$ciceksepetiFlag && $marketplaceType === 'Ciceksepeti') {
                            continue;
                        }
                        if (!$hepsiburadaFlag && $marketplaceType === 'Hepsiburada') {
                            continue;
                        }
                        if (!$wayfairFlag && $marketplaceType === 'Wayfair') {
                            continue;
                        }
                        if (!$ozonFlag && $marketplaceType === 'Ozon') {
                            continue;
                        }
                    }
                }

                $notificationMessage .= "{$marketplaceType}-{$marketplace->getKey()} ";
                echo "Processing {$marketplaceType} Marketplace {$marketplace->getKey()} ...\n";
                $connector = match ($marketplaceType) {
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
                if ($downloadFlag || $importFlag || $updateFlag) {
                    echo "    Downloading... ";
                    $connector->download($downloadFlag);
                    echo "done.\n";
                    $notificationMessage .= "- Listings downloaded\n";
                }
                if ($updateFlag || $importFlag) {
                    echo "    Importing...";
                    $connector->import($updateFlag, $importFlag);
                    echo "done.\n";
                    $notificationMessage .= "- Listings imported ";
                    $notificationSendFlag = true;
                }
                if ($ordersFlag) {
                    echo "    Getting orders... ";
                    $connector->downloadOrders();
                    $notificationMessage .= "- Ordes downloaded";
                }
                if ($inventoryFlag) {
                    echo "    Getting inventory... ";
                    $connector->downloadInventory();
                    $notificationMessage .= "- Inventory downloaded";
                }
                if ($attributesFlag) {
                    echo "    Getting attributes... ";
                    $connector->downloadAttributes();
                }
                echo "done.\n";
                $notificationMessage .= "\n";
            }
            if ($notificationSendFlag) {
              $this->notificationService->sendToUser(2, 1, 'Import completed!', $notificationMessage);
            }
            $this->addListeners();
            return Command::SUCCESS;
        } catch (Exception|\Exception) {
            $this->notificationService->sendToUser(2, 1, 'Import failed!', "An error occurred while importing listings. Here is where it stopped:\n$notificationMessage");
            $this->addListeners();
            return Command::FAILURE;
        }
    }
}