<?php

namespace App\Command;

use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Model\DataObject\GroupProduct;

#[AsCommand(
    name: 'app:hello-world',
    description: 'Outputs Hello, World!'
)]
class HelloWorldCommand extends AbstractCommand
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sql = "select * from iwa_inventory where asin = 'B089463VG3' and inventory_type = 'AMAZON_FBA'";
        $result = Utility::fetchFromSql($sql);
        foreach ($result as $row) {
            $json = $row['json_data'];
            $data = json_decode($json, true);
            $seller_sku = $data['sellerSku'];
            echo "Seller sku: " . $seller_sku . "\n";
        }
        return Command::SUCCESS;
    }
}