<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Product\Listing;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\EventListener\DataObjectListener;
use Symfony\Component\Finder\Finder;
use Pimcore\Model\Asset;
use App\Utils\AmazonConnector;
use App\Utils\ShopifyConnector;
use App\Utils\EtsyConnector;
use App\Utils\TrendyolConnector;


#[AsCommand(
    name: 'app:import',
    description: 'Imports products from Shopify sites!'
)]
class ImportCommand extends AbstractCommand
{
    private static $downloadFlag = false;
    private static $importFlag = false;
    private static $updateFlag = false;
    private static $generateFlag = false;
    private static $matchFlag = false;
    private static $marketplaceArg = null;
    private static $resetVariantsFlag = null;
    private static $skipAmazonFlag = false;
    private static $skipEtsyFlag = false;
    private static $skipShopifyFlag = false;
    private static $skipTrendyolFlag = false;

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
            ->addArgument('marketplace', InputOption::VALUE_OPTIONAL, 'The marketplace to import from.')
            ->addOption('list', null, InputOption::VALUE_NONE, 'If set, Lists all possible objects for processing.')
            ->addOption('download', null, InputOption::VALUE_NONE, 'If set, Shopify listing data will always be downloaded.')
            ->addOption('import', null, InputOption::VALUE_NONE, 'If set, downloaded listing data will be imported to create missing Shopify objects.')
            ->addOption('update', null, InputOption::VALUE_NONE, 'If set, existing Shopify objects will be updated.')
            ->addOption('transfer', null, InputOption::VALUE_NONE, 'If set, existing Shopify objects will be transfered.')
            ->addOption('generate', null, InputOption::VALUE_NONE, 'If set, Shopify objects will be used to create Product objects.')
            ->addOption('match', null, InputOption::VALUE_NONE, 'If set, new Shopify objects will be matched tu current Product objects.')
            ->addOption('images', null, InputOption::VALUE_NONE, 'If set, images will be imported to products.')
            ->addOption('skip-amazon', null, InputOption::VALUE_NONE, 'If set, Amazon objects will be skipped.')
            ->addOption('skip-etsy', null, InputOption::VALUE_NONE, 'If set, Etsy objects will be skipped.')
            ->addOption('skip-shopify', null, InputOption::VALUE_NONE, 'If set, Shopify objects will be skipped.')
            ->addOption('skip-trendyol', null, InputOption::VALUE_NONE, 'If set, Trendyol objects will be skipped.')
            ;
    }

    private static function getMarketplaceObjects($type = null): array
    {
        $list = new Marketplace\Listing();
        if (!empty($type)) {
            $list->setCondition("`marketplaceType` = ?", [$type]);
        }
        $marketplaces = $list->load();
        return $marketplaces;
    }

    private function removeListeners()
    {
        $this->eventDispatcher->removeSubscriber($this->dataObjectListener);
        return;
        $this->eventDispatcher->removeListener('pimcore.dataobject.preAdd', [$this->dataObjectListener, 'onPreAdd']);
        $this->eventDispatcher->removeListener('pimcore.dataobject.preUpdate', [$this->dataObjectListener, 'onPreUpdate']);
        $this->eventDispatcher->removeListener('pimcore.dataobject.onPostLoad', [$this->dataObjectListener, 'onPostLoad']);
        $this->eventDispatcher->removeListener('pimcore.dataobject.onPostUpdate', [$this->dataObjectListener, 'onPostUpdate']);
        $this->eventDispatcher->removeListener('pimcore.dataobject.preDelete', [$this->dataObjectListener, 'onPreDelete']);
    }

    private function addListeners()
    {
        $this->eventDispatcher->addSubscriber($this->dataObjectListener);
        return;
        $this->eventDispatcher->addListener('pimcore.dataobject.preAdd', [$this->dataObjectListener, 'onPreAdd']);
        $this->eventDispatcher->addListener('pimcore.dataobject.preUpdate', [$this->dataObjectListener, 'onPreUpdate']);
        $this->eventDispatcher->addListener('pimcore.dataobject.onPostLoad', [$this->dataObjectListener, 'onPostLoad']);
        $this->eventDispatcher->addListener('pimcore.dataobject.onPostUpdate', [$this->dataObjectListener, 'onPostUpdate']);
        $this->eventDispatcher->addListener('pimcore.dataobject.preDelete', [$this->dataObjectListener, 'onPreDelete']);
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

    protected function importImagesToProducts()
    {
        $imageDirectory = PIMCORE_PROJECT_ROOT . '/tmp/images/';
        $finder = new Finder();
        $finder->files()->in($imageDirectory)->name('*.png');

        // Load or create the _default_upload_bucket folder
        $defaultFolder = Asset::getByPath('/_default_upload_bucket');
        if (!$defaultFolder) {
            $defaultFolder = new Asset\Folder();
            $defaultFolder->setFilename('_default_upload_bucket');
            $defaultFolder->setParent(Asset::getByPath('/')); // Setting root as parent
            $defaultFolder->save();
        }

        foreach ($finder as $file) {
            if (@getimagesize($file->getRealPath()) === false) {
                unlink($file->getRealPath());
                echo "Deleted non-image file: {$file->getFilename()}\n";
                continue;
            }

            // Extract the filename without extension
            $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);

            // Find the corresponding Product object by productIdentifier
            $product = Product::getByProductIdentifier($filename, ['limit' => 1,'unpublished' => true]);

            if ($product instanceof Product) {
                echo "Found Product {$filename}...";
                $image = $product->getImage();
                if ($image) {
                    echo "Product already has an image: $filename\n";
                    unlink($file->getRealPath());
                    continue;
                }

                // Create a new image asset in the _default_upload_bucket folder
                $imageAssetPath = $file->getRealPath();
                $imageAsset = new Asset\Image();
                $imageAsset->setFilename($file->getFilename());
                $imageAsset->setData(file_get_contents($imageAssetPath));
                $imageAsset->setParent($defaultFolder);
                $imageAsset->save();

                // Ensure the asset is properly set with ID and type
                $imageAsset = Asset::getById($imageAsset->getId()); // Reload the asset to ensure ID is set

                if ($imageAsset instanceof Asset\Image) {
                    // Directly set the image asset in the Product's image field
                    $product->setImage($imageAsset);

                    // Save the Product object
                    $product->save();
                    $product->checkAssetFolders();

                    echo "Set image {$file->getFilename()} for Product {$filename}\n";
                    unlink($file->getRealPath());
                } else {
                    echo "Failed to load image asset for {$filename}\n";
                }
            } else {
                echo "No Product found with productIdentifier {$filename}\n";
            }
        }
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        self::$downloadFlag = $input->getOption('download');
        self::$importFlag = $input->getOption('import');
        self::$updateFlag = $input->getOption('update');
        self::$generateFlag = $input->getOption('generate');
        self::$matchFlag = $input->getOption('match');
        self::$marketplaceArg = $input->getArgument('marketplace');
        self::$skipAmazonFlag = $input->getOption('skip-amazon');
        self::$skipEtsyFlag = $input->getOption('skip-etsy');
        self::$skipShopifyFlag = $input->getOption('skip-shopify');
        self::$skipTrendyolFlag = $input->getOption('skip-trendyol');

        $this->removeListeners();

        try {
            if ($input->getOption('images')) {
                self::importImagesToProducts();
                return Command::SUCCESS;
            }

            if ($input->getOption('list')) {
                return self::listMarketplaces();
            }

            $marketplaces = self::getMarketplaceObjects();
            foreach ($marketplaces as $marketplace) {
                if (!empty(self::$marketplaceArg) && !in_array($marketplace->getKey(), self::$marketplaceArg)) {
                    continue;
                }
                if (self::$skipAmazonFlag && $marketplace->getMarketplaceType() == 'Amazon') {
                    continue;
                }
                if (self::$skipEtsyFlag && $marketplace->getMarketplaceType() == 'Etsy') {
                    continue;
                }
                if (self::$skipShopifyFlag && $marketplace->getMarketplaceType() == 'Shopify') {
                    continue;
                }
                if (self::$skipTrendyolFlag && $marketplace->getMarketplaceType() == 'Trendyol') {
                    continue;
                }
                echo "Processing {$marketplace->getMarketplaceType()} Marketplace {$marketplace->getKey()} ...\n";
                $connector = match ($marketplace->getMarketplaceType()) {
                    'Amazon' => new AmazonConnector($marketplace),
                    'Etsy' => new EtsyConnector($marketplace),
                    'Shopify' => new ShopifyConnector($marketplace),
                    'Trendyol' => new TrendyolConnector($marketplace),
                    'Bol.com' => new BolConnector($marketplace),
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
                echo "    Getting orders... ";
                $connector->downloadOrders();
                echo "done.\n";
            }
            self::prepareShopifyLineItems();
        } finally {
            $this->addListeners();
        }
        return Command::SUCCESS;
    }
}
