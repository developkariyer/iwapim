<?php

namespace App\Command;

use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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

    protected function configure(): void
    {
        $this->setDescription('Ciceksepeti product searches and listings')
            ->addArgument('productCodes', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'One or more product codes to search and list.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $productCodes = $input->getArgument('productCodes');
        echo "Ciceksepeti Command - Product Codes: " . implode(', ', $productCodes) . "\n";

        foreach ($productCodes as $productCode) {
            echo "Started process: $productCode\n";
            $productData = $this->searchProductAndReturnIds($productCode);
            $productId = $productData['product_id'];
            $variantIds = $productData['variantIds'];

            $ciceksepetiMessage = new ProductListingMessage(
                'list',
                $productId,
                265384,
                'admin',
                $variantIds,
                [],
                1,
                'test'
            );
            $stamps = [new TransportNamesStamp(['ciceksepeti'])];
            $this->bus->dispatch($ciceksepetiMessage, $stamps);

            echo "Request sent to queue: $productCode\n";
        }
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
        if (!is_array($product) || empty($product) || !isset($product[0]['oo_id'])) {
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
        return $productData;
    }

}