<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
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
use App\Utils\AmazonConnector;
use App\Utils\Utility;

#[AsCommand(
    name: 'app:clean',
    description: 'Fix tags for imported objects!'
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
            } catch (\Exception $e) {
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
        return Command::SUCCESS;
    }

    private static function fixProducts()
    {
        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(true);
        $pageSize = 50;
        $offset = 0;

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
                $dirty = false;
                switch ($product->level()) {
                    case 0:
                        if ($product->checkProductCode()) {
                            $dirty = true;
                        }
                        foreach (Product::$level0NullFields as $field) {
                            if (!empty($product->get($field))) {
                                echo "\nLevel 0 product: {$product->getId()} {$field} is not null\n";
                                $dirty = true;
                                $product->set($field, null);
                            }
                        }
                        break;
                    case 1:
                        if ($product->checkProductCode()) {
                            $dirty = true;
                        }
                        if ($product->checkIwasku(true)) {
                            $dirty = true;
                        }/*
                        foreach (Product::$level1NullFields as $field) {
                            if (!empty($product->get($field))) {
                                echo "\nLevel 1 product: {$product->getId()} {$field} is not empty\n";
                                $dirty = true;
                                $product->set($field, null);
                            }
                        }*/
                        $size = $product->getVariationSize();
                        if (preg_match('/^(\d+(\.\d+)?)x(\d+(\.\d+)?)cm$/', $size, $matches)) {
                            $width = $matches[1];
                            $height = $matches[3];
                            if (empty($product->getProductDimension1()) || empty($product->getProductDimension2())) {
                                $product->setProductDimension1($width);
                                $product->setProductDimension2($height);
                                echo "Setting dimensions for {$product->getId()} to {$width}x{$height}\n";
                                $dirty = true;
                            }
                        }
                        break;
                    default:
                        echo "?{$product->getId()}\n";
                        break;
                }
                if ($dirty) {
                    echo "Saving...\n";
                    $product->save();
                }
            }
            $offset += $pageSize;
            echo "\rProcessed {$offset}       ";
        }
        echo "Processed {$offset} items   \n";
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

    private static function splitProductFolders($parent)
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

    private static function traverseObjectFolders($objectFolder)
    {
        if ($objectFolder instanceof ObjectFolder) {
            echo "Running in folder: " . $objectFolder->getFullPath() . "\n";
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
                $objectFolder->delete();
                echo "Deleted folder: " . $objectFolder->getFullPath() . "\n";
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
