<?php

namespace App\Command;

use Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\Asset\Folder;
use Pimcore\Model\DataObject\Folder as ObjectFolder;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
use App\Utils\Utility;
use App\Utils\OpenAIChat;

#[AsCommand(
    name: 'app:clean',
    description: 'Fix many things!'
)]
class CleanCommand extends AbstractCommand
{

    static int $level = 0;

    protected function configure()
    {
        $this
            ->addOption('asset', null, InputOption::VALUE_NONE, 'If set, the task will list tagged objects, other options are ignored.')
            ->addOption('object', null, InputOption::VALUE_NONE, 'If set, only new tags will be processed.')
            ->addOption('product-code', null, InputOption::VALUE_NONE, 'If set, only new tags will be processed.')
            ->addOption('asin', null, InputOption::VALUE_NONE, 'If set, connections will be updated using Amazon ASIN values.')
            ->addOption('link-check', null, InputOption::VALUE_NONE, 'If set, VariantProuduct<->Product links will be tested.')
            ->addOption('product-fix', null, InputOption::VALUE_NONE, 'If set, inherited fields will be reset for Products.')
            ->addOption('translate-ai', null, InputOption::VALUE_NONE, 'If set, AI translations will be processed.')
            ->addOption('unpublish', null, InputOption::VALUE_NONE, 'If set, variantProducts not updated in last 3 days will be unpublished.')
            ->addOption('untag-only', null, InputOption::VALUE_NONE, 'If set, only existing tags will be processed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('asset')) {
            self::traverseAssetFolder(Folder::getById(1));
        }
        if ($input->getOption('object')) {
            self::traverseObjectFolders(ObjectFolder::getById(172891));
        }
        if ($input->getOption('product-code')) {
            try {
                Product::setGetInheritedValues(false);
                self::fixProductCodes();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        if ($input->getOption('asin')) {
            //self::transferAsins();
            AmazonConnector::downloadAsins();
        }
        if ($input->getOption('link-check')) {
            self::linkCheck();
        }
        if ($input->getOption('product-fix')) {
            self::fixProducts();
        }
        if ($input->getOption('translate-ai')) {
            self::translateProductNames();
        }
        if ($input->getOption('unpublish')) {
            self::unpublishOlderVariantProducts();
        }
        return Command::SUCCESS;
    }

    private static function unpublishOlderVariantProducts()
    {
        $listingObject = new VariantProduct\Listing();
        $listingObject->setUnpublished(false);
        $listingObject->setCondition("lastUpdate < NOW() - INTERVAL 3 DAY");
        $listingObject->load();
        $totalCount = $listingObject->getTotalCount();
        $index = 0;
        foreach ($listingObject as $variant) {
            $index++;
            echo "Unpublishing: ($index/$totalCount) {$variant->getId()} {$variant->getKey()}\n";
            $variant->setPublished(false);
            $variant->save();
        }
    }

    private static function translateProductNames()
    {
        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(true);
        $pageSize = 50;
        $offset = 0;

        $openAI = new OpenAIChat($_ENV['OPENAI_SECRET']);
        if (!$openAI) {
            echo "OpenAI API is not available\n";
            return;
        }

        while (true) {
            $listingObject->setLimit($pageSize);
            $listingObject->setOffset($offset);
            $products = $listingObject->load();
            if (empty($products)) {
                break;
            }
            foreach ($products as $product) {
                if ($product->level() == 1) {
                    continue;
                }
                if ($product->getNameEnglish()) {
                    if (strpos($product->getNameEnglish(), 'Error:') === false && strpos($product->getNameEnglish(), '=') === false) {
                        //echo "SKIPPED: {$product->getName()} => {$product->getNameEnglish()}\n";
                        continue;
                    }
                }
                $englishName = $openAI->translateProductName($product->getName());
                if ($englishName) {
                    if (strpos($englishName, '=') !== false) {
                        $t = explode('=', $englishName);
                        $englishName = trim($t[1]);
                    }
                    $product->setNameEnglish($englishName);
                    $product->save();
                    echo "{$product->getName()} => {$englishName}\n";
                } else {
                    echo "x {$product->getId()}\n";
                }
            }
            $offset += $pageSize;
            echo "Processed {$offset}\n";
        }
        echo "Finished {$offset} items\n";
    }

    private static function fixProducts()
    {
        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(false);
        $pageSize = 3;
        $offset = 0;
        $index = 0;

        Product::setGetInheritedValues(false);
        DataObject::setGetInheritedValues(false);
        while (true) {
            $listingObject->setLimit($pageSize);
            $listingObject->setOffset($offset);
            $products = $listingObject->load();
            if (empty($products)) {
                break;
            }
            foreach ($products as $product) {
                $dirtReason = "";
                $index++;
                echo "\rProcessing: {$index} {$product->getId()}   ";
                $dirty = false;
                if ($product->checkProductCode()) {
                    $dirty = true;
                    $dirtReason .= "PC ";
                }
                switch ($product->level()) {
                    case 0:
                        foreach (Product::$level0NullFields as $field) {
                            if (!empty($product->get($field))) {
                                $dirty = true;
                                $dirtReason .= "N0_$field ";
                                $product->set($field, null);
                            }
                        }
                        break;
                    case 1:
                        if ($product->checkIwasku()) {
                            $dirty = true;
                            $dirtReason .= "IW ";
                        }/*
                        foreach (Product::$level1NullFields as $field) {
                            if (!empty($product->get($field))) {
                                $dirty = true;
                                $dirtReason .= "N1_$field ";
                                $product->set($field, null);
                            }
                        }*/
                        if (!$product->getRequiresIwasku()) {
                            $dirty = true;
                            $dirtReason .= "RI ";
                            $product->setRequiresIwasku(true);
                        }
                        $listingUniqueIds = "";
                        foreach ($product->getListingItems() as $listingItem) {
                            $listingUniqueIds .= "{$listingItem->getUniqueMarketplaceId()} ";
                        }
                        $listingUniqueIds = trim($listingUniqueIds);
                        if ($listingUniqueIds !== $product->getListingUniqueIds()) {
                            $dirty = true;
                            $dirtReason .= "ID ";
                            $product->setListingUniqueIds($listingUniqueIds);
                        }
                        break;
                    default:
                        echo "?{$product->getId()}\n";
                        break;
                }
                if ($dirty) {
                    echo "Saving... $dirtReason ";
                    echo $product->save() ? "OK\n" : "ERROR\n";
                }
            }
            $offset += $pageSize;
        }
        echo "Processed {$offset} items                              \n";
    }

    private static function linkCheck()
    {
        $multipleMainProducts = [];
        $listingObject = new VariantProduct\Listing();
        $listingObject->setUnpublished(true);
        $pageSize = 50;
        $offset = 0;
        while (true) {
            $listingObject->setLimit($pageSize);
            $listingObject->setOffset($offset);
            $variants = $listingObject->load();
            if (empty($variants)) {
                break;
            }
            foreach ($variants as $variant) {
                $mainProductArray = $variant->getMainProduct();
                if (count($mainProductArray) > 1) {
                    echo "*************Multiple main products for variantProduct: {$variant->getId()}\n";
                    $multipleMainProducts[] = $variant->getId();
                }                    
            }
            $offset += $pageSize;
            echo "Processed {$offset}\n";
        }
        print_r($multipleMainProducts);
    }

    private static function transferAsins()
    {
        function readAsinFromDb($id) {
            $db = \Pimcore\Db::get();
            $jsonData = $db->fetchOne("SELECT json_data FROM iwa_json_store WHERE object_id = ? AND field_name = 'apiResponseJson' LIMIT 1", [$id]);
            $data = json_decode($jsonData ?? [], true);
            return $data['summaries'][0]['asin'] ?? $data['asin'] ?? null;
        }

        $stack = [ObjectFolder::getById(223695)];
        while (!empty($stack)) {
            $folder = array_pop($stack);
            if ($folder instanceof ObjectFolder) {
                echo "Running in folder: " . $folder->getFullPath() . "                             \r";
                foreach ($folder->getChildren() as $child) {
                    if ($child instanceof ObjectFolder) {
                        $stack[] = $child;
                    }
                    if ($child instanceof VariantProduct) {
                        if (!($asin = readAsinFromDb($child->getId()))) {
                            continue;
                        }
                        if (!($newVariantProduct = VariantProduct::findOneByField('uniqueMarketplaceId', $asin, unpublished: true))) {
                            echo "\nNo new variant found for {$child->getId()} with ASIN $asin\n";
                        } else {
                            $mainProductArray = $child->getMainProduct();
                            if ($mainProduct = reset($mainProductArray)) {
                                $mainProduct->addVariant($newVariantProduct);
                                echo "{$child->getId()} => {$newVariantProduct->getId()} ";
                                $child->delete();
                                echo "{$child->getId()} deleted\n";                
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @throws Exception
     */
    private static function fixProductCodes()
    {
        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(true);
        $pageSize = 50;
        $offset = 0;

        while (true) {
            $listingObject->setLimit($pageSize);
            $listingObject->setOffset($offset);
            $products = $listingObject->load();
            if (empty($products)) {
                break;
            }
            foreach ($products as $product) {
                if ($product->level() == 1) {
                    $product->checkProductCode();
                    $product->checkIwasku(true);
                    $product->save();
                    echo "s";
                } else {
                    echo "0";
                }
            }
            echo "\nProcessed {$offset} ";
            $offset += $pageSize;
        }
    }

    /**
     * @throws DuplicateFullPathException
     * @throws Exception
     */
    private static function splitProductFolders($parent): void
    {
        foreach ($parent->getChildren() as $category) {
            if ($category instanceof ObjectFolder) {
                echo "Running in folder: " . $category->getFullPath() . "\n";
                $folder = [];
                for ($t=0;$t<20;$t++) {
                    $min = str_pad($t * 20 + 1, 3, "0", STR_PAD_LEFT);
                    $max = str_pad(($t + 1) * 20, 3, "0", STR_PAD_LEFT);
                    $folder[$t] = Utility::checkSetPath("$min-$max", $category);
                }
                foreach ($category->getChildren() as $product) {
                    if ($product instanceof Product) {
                        echo "    Moving product: " . $product->getFullPath() . "\n";
                        preg_match('/\d+/', $product->getProductIdentifier(), $matches);
                        if (is_numeric($matches[0])) {
                            $index = intval($matches[0]) / 20;
                            $product->setParent($folder[$index]);
                            $product->save();
                        }
                    }
                }
            }
        }
    }

    private static function traverseObjectFolders($objectFolder): void
    {
        if ($objectFolder instanceof ObjectFolder) {
            echo "\rRunning in folder: " . $objectFolder->getFullPath() . " ";
            $childCount = 0;
            foreach ($objectFolder->getChildren() as $child) {
                $childCount++;
                if ($child instanceof ObjectFolder) {
                    self::traverseObjectFolders($child);
                }
                if ($child instanceof Product) {
                    //echo ".";
                    //$child->save();
                    //echo "Saved: " . $child->getFullPath() . "\n";
                    //self::traverseObjectFolders($child);
                }
            }
            if ($childCount === 0) {
                //$objectFolder->delete();
                echo "\nDeleted folder: " . $objectFolder->getFullPath() . "\n";
            }
        }
    }

    private static function traverseAssetFolder($assetFolder)
    {
        if ($assetFolder->getFullPath() === "/Image Cache") {
            return;
        }
        static::$level++;
        echo str_pad('', static::$level, ' ')."Found folder: " . $assetFolder->getFullPath() . "\r";
        if ($assetFolder instanceof Folder) {
            $childCount = 0;
            foreach ($assetFolder->getChildren() as $child) {
                $childCount++;
                if ($child instanceof Folder) {
                    self::traverseAssetFolder($child);
                }
            }
            if ($childCount === 0) {
                $assetFolder->delete();
                echo str_pad('', static::$level, ' ')."***************Deleted folder: " . $assetFolder->getFullPath() . "\n";
            } else {
                echo str_pad("", static::$level, " ")."Folder not empty: " . $assetFolder->getFullPath() . "\r";
            }
        }
        static::$level--;
    }
}
