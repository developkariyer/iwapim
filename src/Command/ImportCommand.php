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

/*
### **Areas of Concern and Recommendations**

1. **Overuse of Static Variables:**
   - The command relies heavily on static variables to store options and arguments (`$downloadFlag`, `$importFlag`, etc.). This approach can lead to issues with testability and maintainability, especially in concurrent or multi-threaded environments.
     - **Recommendation:** Use instance variables instead of static variables for storing state within the command execution. This will make the code more modular and easier to test.

2. **Direct `echo` Statements:**
   - The code uses `echo` statements for output, which is not the best practice in Symfony commands. This can make it difficult to manage output in different environments (e.g., logging vs. console output).
     - **Recommendation:** Use the `$output->writeln()` method for outputting information. This allows better integration with Symfony's console component and provides more flexibility in managing output.

3. **Inconsistent Use of Event Listeners:**
   - The methods `removeListeners()` and `addListeners()` are intended to manage event listeners, but they contain commented-out code that suggests an incomplete or uncertain implementation.
     - **Recommendation:** Clean up the commented-out code or fully implement the listener management. If certain listeners are not needed, it's better to remove them rather than leaving them commented out.

4. **Hardcoded File Paths:**
   - The `importImagesToProducts()` method uses a hardcoded path (`PIMCORE_PROJECT_ROOT . '/tmp/images/'`), which reduces flexibility and might cause issues if the directory structure changes.
     - **Recommendation:** Make file paths configurable through environment variables or a configuration file, allowing for more flexibility and adaptability to different environments.

5. **Insufficient Error Handling for File Operations:**
   - The `importImagesToProducts()` method directly manipulates files (e.g., deleting non-image files) without comprehensive error handling. If the file system is not accessible or if permissions are incorrect, this could lead to failures.
     - **Recommendation:** Add error handling around file operations to ensure that the script fails gracefully if it encounters file-related issues.

6. **Potential SQL Injection Risk:**
   - The SQL queries in `prepareShopifyLineItems()` are written using raw SQL with user-supplied data. While the query itself is unlikely to be directly exposed to user input, this approach is generally less safe.
     - **Recommendation:** Use parameterized queries or a query builder provided by Pimcore's DBAL to minimize the risk of SQL injection and improve code readability.

7. **Heavy Reliance on `try-finally`:**
   - The use of `try-finally` is appropriate for ensuring listeners are re-added, but the logic within the `try` block is complex and could benefit from being broken down into smaller, more manageable methods.
     - **Recommendation:** Refactor the `execute` method to break down the logic into smaller, dedicated methods for each task (e.g., `processMarketplace`, `downloadData`, `importData`). This will make the code easier to understand and maintain.

8. **Tight Coupling Between Command and Connectors:**
   - The command is tightly coupled to the specific connector classes (`AmazonConnector`, `ShopifyConnector`, etc.), making it harder to extend or modify the behavior for different marketplaces.
     - **Recommendation:** Consider using a dependency injection pattern or a factory pattern to manage the creation and use of connectors. This would decouple the command from specific implementations and make the code more flexible.

9. **Inefficient File Handling in `importImagesToProducts()`:**
   - The method loads each image file into memory with `file_get_contents()` before creating an asset, which can be inefficient for large files or a large number of files.
     - **Recommendation:** Consider streaming the file contents directly to the asset instead of loading the entire file into memory. This will reduce memory usage and improve performance.

10. **Unclear Flow of Execution:**
    - The `execute` method handles multiple operations (downloading, importing, processing images) in a sequential manner, which can make the flow of execution unclear and prone to errors.
      - **Recommendation:** Structure the command to separate concerns more clearly, perhaps by using Symfony events or dedicated service classes for each major operation (e.g., downloading, importing, image processing).

11. **Lack of Error Handling in Download and Import Operations:**
    - The operations for downloading and importing data from marketplaces do not have robust error handling. If a download or import fails, the script will continue without addressing the failure.
      - **Recommendation:** Implement error handling for the download and import processes, ensuring that failures are logged or reported, and that the script can recover or exit gracefully.

12. **Lack of Logging:**
    - There is no logging mechanism in place to track the progress or errors, which makes it difficult to diagnose issues in production.
      - **Recommendation:** Integrate a logging library (such as Monolog) to log important events, errors, and progress. This will help in debugging and monitoring the application's behavior.

By addressing these issues, the command will be more robust, maintainable, and easier to test and extend.
*/