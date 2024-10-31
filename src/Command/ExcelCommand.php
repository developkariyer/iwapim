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

class ErrorListingsCommand extends AbstractCommand
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
        $products->setCondition('requiredIwasku = true');
        $products = $products->load();
        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'key' => $product->getKey(),
                'iwasku' => $product->getIwasku(),
                'variationSize' => $product->getVariationSize(),
                'variationColor' => $product->getVariationColor(),
                'productDimension1' => $product->getProductDimension1(),
                'productDimension2' => $product->getProductDimension2(),
                'productDimension3' => $product->getProductDimension3(),
                'productWeight' => $product->getProductWeight(),
                'packageDimension1' => $product->getPackageDimension1(),
                'packageDimension2' => $product->getPackageDimension2(),
                'packageDimension3' => $product->getPackageDimension3(),
                'packageWeight' => $product->getPackageWeight(),
                'category' => $product->getProductCategory(),
            ];
        }
        $this->writeCsv(PIMCORE_PROJECT_ROOT . '/tmp/products.csv', $data);
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