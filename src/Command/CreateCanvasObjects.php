<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Folder as ObjectFolder;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\VariantProduct;
use App\Utils\Utility;

#[AsCommand(
    name: 'app:create-canvas',
    description: 'Create canvas objects from Shopify IWA EN!'
)]
class CreateCanvasObjects extends AbstractCommand
{

    protected function configure()
    {
        $this
            ->addOption('asset', null, InputOption::VALUE_NONE, 'If set, the task will list tagged objects, other options are ignored.')
            ->addOption('object', null, InputOption::VALUE_NONE, 'If set, only new tags will be processed.')
            ->addOption('product-code', null, InputOption::VALUE_NONE, 'If set, only new tags will be processed.')
            ->addOption('untag-only', null, InputOption::VALUE_NONE, 'If set, only existing tags will be processed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //173940
        $objectFolder = ObjectFolder::getById(173940);
        $canvasFolder = ObjectFolder::getById(221102);
        $pId = 1;
        foreach ($objectFolder->getChildren() as $childFolder) {
            if (!$childFolder instanceof ObjectFolder) {
                continue;
            }
            echo "Running in folder: " . $childFolder->getFullPath() . "\n";
            $productIdentifier = "KV-".str_pad($pId, 3, "0", STR_PAD_LEFT);
            $productName = trim(preg_replace('/\s+/', ' ', str_replace(['-', 'Canvas', 'Printing'], ' ', $childFolder->getKey())));
            $variants = $childFolder->getChildren();
            if (count($variants) > 0) {
                echo "    Found product: " . $productName . " with ".count($variants)." variants.\n";
                $product = Product::findByField('productIdentifier', $productIdentifier);
                if (!$product) {
                    $product = new Product();
                    $product->setPublished(true);
                    $product->setParent($canvasFolder);
                    $product->setProductIdentifier($productIdentifier);
                    $product->setName($productName);
                    $product->checkProductCode();
                    $product->checkKey();
                    $product->save();
                }
            }
            foreach ($variants as $variant) {
                if ($variant instanceof VariantProduct) {
                    echo "        Found variant: " . $variant->getKey() . "\n";
                    $size = $variant->getAttributes();
                    $size = explode("|", $size);
                    $size = (count($size) > 1) ? $size[1] : $size[0];
                    $size = str_replace(" ", "", $size);
                    $color = 'Standart';
                    $subProduct = new Product();
                    $subProduct->setPublished(true);
                    $subProduct->setParent($product);
                    $subProduct->setVariationSize($size);
                    $subProduct->setVariationColor($color);
                    $subProduct->checkProductCode();
                    $subProduct->checkKey();
                    $subProduct->checkIwasku();
                    try {
                        echo "            Saving variant: $size\n";
                        $subProduct->save();
                    } catch (\Exception $e) {
                        echo $e->getMessage();
                    }
                }
            }
            $pId++;
            if ($pId > 1) {
                break;
            }
        }
        return Command::SUCCESS;
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

}
