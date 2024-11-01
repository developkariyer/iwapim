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
        return Command::SUCCESS;
    }

    private function dumpProducts()
    {
        $products = new Product\Listing();
        $products->setUnpublished(false);
        $products->setCondition('requiresIwasku = true');
        echo "Loading products...";
        $products = $products->load();
        $data = [];
        echo "\n";
        $index = 0;
        foreach ($products as $product) {
            $index++;
            echo "\rProcessing product $index";
            $category = $product->getInheritedField('productCategory');
            if (!isset($data[$category])) {
                $data[$category] = [];
            }
            $data[$category][] = [
                'id' => $product->getId(),
                'name' => $product->getProductIdentifier().' '.$product->getInheritedField('name'),
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
        }
        echo "\n";
        $flatfile = [];
        foreach ($data as $category=>$products) {
            $this->writeCsv(PIMCORE_PROJECT_ROOT . '/public/products_' . $category . '.csv', $products);
            echo "Products dumped to public/products_$category.csv\n";
            $flatfile = array_merge($flatfile, $products);
        }
        $this->writeCsv(PIMCORE_PROJECT_ROOT . '/public/products.csv', $flatfile);
        echo "Products dumped to public/products.csv\n";
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