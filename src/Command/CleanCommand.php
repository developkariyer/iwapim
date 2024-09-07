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
use Pimcore\Model\DataObject\Marketplace;
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
            ->addOption('untag-only', null, InputOption::VALUE_NONE, 'If set, only existing tags will be processed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('asset')) {
            self::traverseAssetFolder(Folder::getById(1));
        }
        if ($input->getOption('object')) {
            self::traverseObjectFolders(ObjectFolder::getById(149861));
        }
        if ($input->getOption('product-code')) {
            try {
                Product::setGetInheritedValues(false);
                self::fixProductCodes();
            } catch (\Exception $e) {
                echo $e->getMessage();
            } finally {
                Product::setGetInheritedValues(true);
            }
        }
        if ($input->getOption('asin')) {
            self::crossCheckAsins();
        }
        return Command::SUCCESS;
    }

    //self::splitProductFolders(ObjectFolder::getById(149861));

    private static function crossCheckAsins()
    {
        $db = \Pimcore\Db::get();
        $sql = "SELECT DISTINCT amazonAsin FROM object_varyantproduct WHERE amazonAsin IS NOT NULL";
        $stmt = $db->query($sql);
        $asins = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        //print_r($asins);
        foreach ($asins as $asin) {
            if (empty($asin)) {
                continue;
            }
            echo "\rProcessing ASIN: {$asin}                                             \r";
            $listingObject = new VariantProduct\Listing();
            $listingObject->setCondition("amazonAsin = ?", [$asin]);
            $listingObject->setUnpublished(true);
            $variants = $listingObject->load();
            $connectedProduct = [];
            echo "\n    Found ".count($variants) . " variants\n";
            foreach ($variants as $variant) {
                $mainProduct = $variant->getMainProduct();
                if (empty($mainProduct)) {
                    continue;
                }
                if (count($mainProduct) > 1) {
                    echo "    WARNING: Found more than one main product for variant: {$variant->getId()} " . $variant->getFullPath() . "\n";
                    exit;
                }
                $connectedProduct[] = reset($mainProduct);
            }
            $connectedProduct = array_unique($connectedProduct);
            if (count($connectedProduct) == 1) {
                $product = reset($connectedProduct);
                $product->addVariant($variants);
            } elseif (count($connectedProduct) > 1) {
                echo "    WARNING: Found more than one main product for variants: " . implode(", ", $connectedProduct) . "\n";
                exit;
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
