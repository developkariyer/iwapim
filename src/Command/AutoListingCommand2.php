<?php

namespace App\Command;

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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\ProductListingMessage;
use Symfony\Component\Messenger\Stamp\TransportNamesStamp;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputOption;
use App\Model\DataObject\Marketplace;
use App\Connector\Marketplace\CiceksepetiConnector;
use App\Logger\LoggerFactory;

#[AsCommand(
    name: 'app:autolisting2',
    description: 'Outputs Hello, World!'
)]
class AutoListingCommand2 extends AbstractCommand
{
    public function __construct(private MessageBusInterface $bus)
    {
        parent::__construct();
        $this->logger = LoggerFactory::create('ciceksepeti','auto_listing');
    }

    private array $marketplaceConfig = [
        'Ciceksepeti' => 265384,
        'ShopifyCfwTr' => 84124,
    ];

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->syncMarketplaceProducts('ShopifyCfwTr', 'Ciceksepeti');
        return Command::SUCCESS;
    }

    private function syncMarketplaceProducts($fromMarketplace, $toMarketplace)
    {
        echo "Syncing $fromMarketplace to $toMarketplace\n";
        $this->logger->info("[" . __METHOD__ . "] 🚀 Syncing $fromMarketplace to $toMarketplace");
        $fromMarketplaceVariantIds = $this->getMarketplaceVariantIds($fromMarketplace);
        $updateProductList = [];
        $newProductList = [];
        $noMainProductCount = 0;
        $manyMainProductCount = 0;
        $variantCountWithMainProduct = 0;
        foreach ($fromMarketplaceVariantIds as $fromMarketplaceVariantId) {
            $fromMarketplaceVariantProduct = VariantProduct::getById($fromMarketplaceVariantId);
            if (!$fromMarketplaceVariantProduct instanceof VariantProduct) {
                echo "Invalid $fromMarketplaceVariantId, skipping...\n";
                $this->logger->warning("[" . __METHOD__ . "] ⚠️ Invalid $fromMarketplaceVariantId, skipping...");
                continue;
            }
            $fromMarketplaceMainProducts = $fromMarketplaceVariantProduct->getMainProduct();
            if (!is_array($fromMarketplaceMainProducts) || empty($fromMarketplaceMainProducts)) {
                $noMainProductCount++;
                $this->logger->warning("[" . __METHOD__ . "] ⚠️ $fromMarketplaceVariantId has no main product ");
                continue;
            }
            if (count($fromMarketplaceMainProducts) > 1) {
                $manyMainProductCount++;
                $this->logger->warning("[" . __METHOD__ . "] ⚠️ $fromMarketplaceVariantId has many main products ");
                continue;
            }
            $fromMarketplaceMainProduct = $fromMarketplaceMainProducts[0];
            if (!$fromMarketplaceMainProduct instanceof Product) {
                $this->logger->warning("[" . __METHOD__ . "] ⚠️ $fromMarketplaceVariantId has invalid main product ");
                continue;
            }
            $iwasku = $fromMarketplaceMainProduct->getIwasku();
            if (!$iwasku) {
                $this->logger->warning("[" . __METHOD__ . "] ⚠️ $fromMarketplaceVariantId has no iwasku ");
            }
            $variantCountWithMainProduct++;
        }
        $this->logger->warning("[" . __METHOD__ . "] ⚠️ $noMainProductCount products has no main product ");
        $this->logger->warning("[" . __METHOD__ . "] ⚠️ $manyMainProductCount products main product count is more than 1 ");
        $this->logger->info("[" . __METHOD__ . "] ✅ $variantCountWithMainProduct products has main product  ");


    }

    private function getMarketplaceVariantIds(string $marketplace): array | null
    {
        $variantProductQuerySql = "SELECT oo_id FROM object_query_varyantproduct WHERE marketplace__id = :marketplace_id";
        $variantProductIds = Utility::fetchFromSql($variantProductQuerySql, ['marketplace_id' => $this->marketplaceConfig[$marketplace]]);
        if (!is_array($variantProductIds) || empty($variantProductIds)) {
            echo "Marketplace $marketplace variant product ids not found\n";
            $this->logger->error("[" . __METHOD__ . "] ❌ Marketplace $marketplace variant product ids not found");
            return null;
        }
        $count = count($variantProductIds);
        echo "Marketplace $marketplace variant product ids found: $count\n";
        $this->logger->info("[" . __METHOD__ . "] ✅ Marketplace $marketplace variant product ids found: $count");
        return array_column($variantProductIds, 'oo_id');
    }


}