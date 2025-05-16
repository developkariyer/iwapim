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
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(
    name: 'app:autolisting',
    description: 'Outputs Hello, World!'
)]
class AutoListingCommand extends AbstractCommand
{
    public function __construct(private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    private array $marketplaceConfig = [
        'ciceksepeti' => ['marketplace_id' => 265384]
    ];

    protected function configure(): void
    {
        $this
            ->setDescription('Auto product listing to specified marketplace')
            ->addOption('marketplace', null, InputOption::VALUE_REQUIRED, 'Marketplace key (e.g., ciceksepeti, trendyol)')
            ->addArgument('productCodes', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'One or more product codes to search and list.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $marketplace = $input->getOption('marketplace');
        if (!isset($this->marketplaceConfig[$marketplace])) {
            $output->writeln("<error>Unsupported marketplace: $marketplace</error>");
            return Command::FAILURE;
        }
        $marketplaceId = $this->marketplaceConfig[$marketplace]['marketplace_id'];
        $productCodes = $input->getArgument('productCodes');
        $output->writeln("Marketplace: $marketplace (Channel ID: $marketplaceId)");
        $output->writeln("Product Codes: " . implode(', ', $productCodes));
        foreach ($productCodes as $productCode) {
            $output->writeln("Started process: $productCode");
            $productData = $this->searchProductAndReturnIds($productCode);
            if (!$productData) {
                $output->writeln("<comment>Product not found for code: $productCode</comment>");
                continue;
            }
            $productId = $productData['product_id'];
            $variantIds = $productData['variantIds'];
            $message = new ProductListingMessage(
                'list',
                $productId,
                $marketplaceId,
                'admin',
                $variantIds,
                [],
                1,
                'test'
            );
            $stamps = [new TransportNamesStamp([$marketplace])];
            $this->bus->dispatch($message, $stamps);
            $output->writeln("Dispatched to queue for: $productCode");
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