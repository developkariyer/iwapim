<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Product;

#[AsCommand(
    name: 'app:excel',
    description: 'Dump excel table'
)]

class ExcelCommand extends AbstractCommand
{
    protected function configure() 
    {
        $this
            ->addOption('products',null, InputOption::VALUE_NONE, 'Dump products to tmp/products.csv')
            ->addOption('listings',null, InputOption::VALUE_NONE, 'Dump listings to tmp/listings.csv')
            ->addOption('costs',null, InputOption::VALUE_NONE, 'Dump costs to tmp/costs.csv')
            ->addOption('categories', null, InputOption::VALUE_NONE, 'Dump categories to tmp/categories.csv')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('products')) {
            $this->dumpProducts();
        }
        if ($input->getOption('listings')) {
            $this->dumpListings();
        }
        if ($input->getOption('costs')) {
            $this->dumpCosts();
        }
        if ($input->getOption('categories')) {
            $this->dumpCategories();
        }    
        return Command::SUCCESS;
    }

    private function dumpCategories()
    {
        $products = new Product\Listing();
        $products->setUnpublished(false);
        echo "Loading categories and products...";
        $products = $products->load();
        $data = [];
        echo "\n";
        $index = 0;
        foreach ($products as $product) {
            $index++;
            echo "\rProcessing product $index {$product->getId()} ";
            if ($product->level()>0) {
                continue;
            }
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getProductIdentifier().' '.$product->getName(),
                'category' => $product->getProductCategory(),
            ];
        }
        echo "\n";
        $this->writeCsv(PIMCORE_PROJECT_ROOT . '/public/categories.csv', $data);
        echo "Categories dumped to public/categories.csv\n";
    }

    private function dumpProducts()
    {
        $products = new Product\Listing();
        $products->setUnpublished(false);
        echo "Loading products...";
        $products = $products->load();
        $data = [];
        $parentProduct = [];
        echo "\n";
        $index = 0;
        foreach ($products as $product) {
            $index++;
            echo "\rProcessing product $index";
            $category = $product->getInheritedField('productCategory');
            if ($product->getRequiresIwasku()) {
                if (!isset($data[$category])) {
                    $data[$category] = [];
                }
                $data[$category][] = [
                    'id' => $product->getId(),
                    'identifier' => $product->getInheritedField('productIdentifier'),
                    'name' => $product->getInheritedField('productIdentifier').' '.$product->getInheritedField('name'),
                    'iwasku' => $product->getIwasku(),
                    'variationSize' => $product->getVariationSize(),
                    'variationColor' => $product->getVariationColor(),
                    'productDimension1' => $product->getInheritedField('productDimension1'),
                    'productDimension2' => $product->getInheritedField('productDimension2'),
                    'productDimension3' => $product->getInheritedField('productDimension3'),
                    'productWeight' => $product->getInheritedField('productWeight'),
                    'packageDimension1' => $product->getInheritedField('packageDimension1'),
                    'packageDimension2' => $product->getInheritedField('packageDimension2'),
                    'packageDimension3' => $product->getInheritedField('packageDimension3'),
                    'packageWeight' => $product->getInheritedField('packageWeight'),
                    'image' => $product->getImageUrl() ? $product->getImageUrl()->getUrl() : '',
                    'category' => $category,
                ];
            } else {
                $parentProduct[] = [
                    'id' => $product->getId(),
                    'identifier' => $product->getInheritedField('productIdentifier'),
                    'name' => $product->getInheritedField('productIdentifier').' '.$product->getInheritedField('name'),
                    'category' => $category,
                ];
            }
        }
        echo "\n";
        $flatfile = [];
        foreach ($data as $category=>$products) {
            $this->writeCsv(PIMCORE_PROJECT_ROOT . '/public/tmp/products_' . $category . '.csv', $products);
            echo "Products dumped to products_$category.csv\n";
            $flatfile = array_merge($flatfile, $products);
        }
        $this->writeCsv(PIMCORE_PROJECT_ROOT . '/public/tmp/products.csv', $flatfile);
        $this->writeCsv(PIMCORE_PROJECT_ROOT . '/public/tmp/parent_products.csv', $parentProduct);
        echo "Products dumped to products.csv, parents dumped to parent_products.csv\n";
    }

    private function writeCsv($filename, $data)
    {
        if (empty($data) || !is_array($data)) {
            return;
        }
        $fp = fopen($filename, 'w');
        fputcsv($fp, array_keys($data[0]));
        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
    }

}