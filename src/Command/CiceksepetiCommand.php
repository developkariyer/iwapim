<?php

namespace App\Command;

use App\Message\CiceksepetiCategoryUpdateMessage;
use App\Message\TestMessage;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\ProductListingMessage;
use Symfony\Component\Messenger\Stamp\TransportNamesStamp;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(
    name: 'app:ciceksepeti',
    description: 'Outputs Hello, World!'
)]
class CiceksepetiCommand extends AbstractCommand
{
    public function __construct(private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        echo "Ciceksepeti Command \n";
        $productData  = $this->searchProductAndReturnIds('IJ-006');
        $productId = $productData['product_id'];
        $variantIds = $productData['variantIds'];

        $ciceksepetiMessage = new ProductListingMessage(
            'list',
            $productId,
            265384,
            'ciceksepetiUser',
            $variantIds,
            [],
            1,
            'test'
        );
        $stamps = [new TransportNamesStamp(['ciceksepeti'])];
        $this->bus->dispatch($ciceksepetiMessage, $stamps);
        echo "Istek CICEKSEPETI kuyruğuna gönderildi.\n";

        return Command::SUCCESS;
    }

    private function searchProductAndReturnIds($productIdentifier)
    {
        $productSql = '
        SELECT oo_id, name, productCategory from object_query_product
        WHERE productIdentifier = :productIdentifier AND productLevel = 0
        LIMIT 1';
        $variantSql = '
        SELECT oo_id, iwasku, variationSize, variationColor FROM object_query_product
        WHERE productIdentifier = :productIdentifier AND productLevel = 1 AND listingItems IS NOT NULL';

        $product = Utility::fetchFromSql($productSql, ['productIdentifier' => $productIdentifier]);
        if (!is_array($product) || empty($product)) {
            return [];
        }

        $variants = Utility::fetchFromSql($variantSql, ['productIdentifier' => $productIdentifier]);
        if (!is_array($variants) || empty($variants)) {
            return [];
        }

        $productData = [
            'product_id' => $product[0]['oo_id']
        ];
        $variantData = [];
        foreach ($variants as $variant) {
            $variantData[] = $variant['oo_id'];
        }
        $productData['variantIds'] = $variantData;
        print_r($productData);
        return $productData;
    }


}