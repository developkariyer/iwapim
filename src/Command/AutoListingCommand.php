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
    name: 'app:autolisting',
    description: 'Autolisting From Marketplace => To Marketplace',
)]
class AutoListingCommand extends AbstractCommand
{
    private array $marketplaceConfig = [
        'Ciceksepeti' => 265384,
        'ShopifyCfwTr' => 84124,
        'HepsiburadaCfw' => 265919
    ];

    public function __construct(private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Marketplace Sync Command')
            ->addOption('source', null, InputOption::VALUE_REQUIRED, 'Source Marketplace Name')
            ->addOption('target', null, InputOption::VALUE_REQUIRED, 'Target Marketplace Name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getOption('source');
        $target = $input->getOption('target');
        if (!$source || !$target) {
            return Command::FAILURE;
        }
        if (!isset($this->marketplaceConfig[$source], $this->marketplaceConfig[$target])) {
            $output->writeln("<error>Invalid Marketplace Name</error>");
            return Command::FAILURE;
        }
        $this->logger = LoggerFactory::create($target, 'auto_listing');
        $this->logger->info("[" . __METHOD__ . "] Sync Start:  $source → $target");
        $this->syncMarketplaceProducts($source, $target);
        $this->logger->info("[" . __METHOD__ . "] Sync Complated .... ");
        return Command::SUCCESS;
    }

    private function syncMarketplaceProducts($fromMarketplace, $toMarketplace)
    {
        echo "Syncing $fromMarketplace to $toMarketplace\n";
        $this->logger->info("[" . __METHOD__ . "] 🚀 Syncing $fromMarketplace to $toMarketplace");
        $fromMarketplaceVariantIds = $this->getFromMarketplaceVariantIds($fromMarketplace);
        $updateProductList = [];
        $newProductList = [];
        $fromMarketplaceNoMainProductCount = 0;
        $fromMarketplaceManyMainProductCount = 0;
        $fromMarketplaceVariantCountWithMainProduct = 0;
        foreach ($fromMarketplaceVariantIds as $fromMarketplaceVariantId) {
            $fromMarketplaceVariantProduct = VariantProduct::getById($fromMarketplaceVariantId);
            if (!$fromMarketplaceVariantProduct instanceof VariantProduct) {
                echo "Invalid $fromMarketplaceVariantId, skipping...\n";
                $this->logger->warning("[" . __METHOD__ . "] ⚠️ From Marketplace $fromMarketplace Invalid variantProductId:$fromMarketplaceVariantId, skipping...");
                continue;
            }
            $fromMarketplaceMainProducts = $fromMarketplaceVariantProduct->getMainProduct();
            if (!is_array($fromMarketplaceMainProducts) || empty($fromMarketplaceMainProducts)) {
                $fromMarketplaceNoMainProductCount++;
                $this->logger->warning("[" . __METHOD__ . "] ⚠️ From Marketplace $fromMarketplace variantProductId:$fromMarketplaceVariantId has no main product ");
                continue;
            }
            if (count($fromMarketplaceMainProducts) > 1) {
                $fromMarketplaceManyMainProductCount++;
                $this->logger->warning("[" . __METHOD__ . "] ⚠️ From Marketplace $fromMarketplace variantProductId:$fromMarketplaceVariantId has many main products ");
                continue;
            }
            $fromMarketplaceMainProduct = $fromMarketplaceMainProducts[0];
            if (!$fromMarketplaceMainProduct instanceof Product) {
                $this->logger->warning("[" . __METHOD__ . "] ⚠️ From Marketplace $fromMarketplace variantProductId:$fromMarketplaceVariantId has invalid main product ");
                continue;
            }
            $iwasku = $fromMarketplaceMainProduct->getIwasku();
            if (!$iwasku) {
                $this->logger->warning("[" . __METHOD__ . "] ⚠️ From Marketplace $fromMarketplace variantProductId:$fromMarketplaceVariantId has no iwasku ");
            }
            $fromMarketplaceVariantCountWithMainProduct++;
            $targetMarketplaceVariantProduct = $this->getTargetMarketplaceVariantProduct($toMarketplace, $iwasku);
            if (!$targetMarketplaceVariantProduct instanceof VariantProduct) {
                $newProductList[] = [
                    'id' => $fromMarketplaceVariantProduct->getId(),
                    'mainCode' => $fromMarketplaceMainProduct->getProductIdentifier()
                ];
            }
            else {
                $updateProductList[] = [
                    'from' => $fromMarketplaceVariantProduct->getId(),
                    'to' => $targetMarketplaceVariantProduct->getId()
                ];
            }
        }
        $groupedByMainCode = $this->groupByMainCode($newProductList);
        $mainProductCount = count($groupedByMainCode);
        $variantCount = count($newProductList);
        $toMarketplaceUpdateProductCount = count($updateProductList);
        $this->logger->warning("[" . __METHOD__ . "] ⚠️ From Marketplace $fromMarketplace Count: $fromMarketplaceNoMainProductCount products find has no main product ");
        $this->logger->warning("[" . __METHOD__ . "] ⚠️ From Marketplace $fromMarketplace Count: $fromMarketplaceManyMainProductCount products find main product count is more than 1 ");
        $this->logger->info("[" . __METHOD__ . "] ✅ From Marketplace $fromMarketplace Count: $fromMarketplaceVariantCountWithMainProduct products find has main product  ");
        $this->logger->info("[" . __METHOD__ . "] ✅ Target Marketplace $toMarketplace contains $mainProductCount main products and $variantCount variants found for syncing.");
        $this->logger->info("[" . __METHOD__ . "] ✅ Target Marketplace $toMarketplace Count: $toMarketplaceUpdateProductCount to marketplace find update products ");
        $this->processUpdateList($updateProductList, $this->marketplaceConfig[$toMarketplace], $this->marketplaceConfig[$fromMarketplace], $toMarketplace);
        $this->processNewList($groupedByMainCode, $this->marketplaceConfig[$toMarketplace], $this->marketplaceConfig[$fromMarketplace], $toMarketplace);
    }

    private function groupByMainCode(array $newProductList): array
    {
        $grouped = [];
        foreach ($newProductList as $item) {
            $mainCode = $item['mainCode'];
            $id = $item['id'];
            if (!isset($grouped[$mainCode])) {
                $grouped[$mainCode] = [];
            }
            $grouped[$mainCode][] = $id;
        }
        return $grouped;
    }

    private function processNewList($groupedByMainCode, $targetMarketplaceId, $referenceMarketplaceId, $toMarketplace): void
    {
        foreach ($groupedByMainCode as $mainCode => $variantIds) {
            $message = new ProductListingMessage(
                'list',
                $targetMarketplaceId,
                $referenceMarketplaceId,
                'admin',
                $variantIds,
                [],
                1,
                'test',
                $this->logger
            );
            $transportName = $this->resolveTransportName($toMarketplace);
            $stamps = [new TransportNamesStamp([$transportName])];
            $this->bus->dispatch($message, $stamps);
            $this->logger->info("[" . __METHOD__ . "] ✅ Created Message for Main Product Code: $mainCode");
        }
    }

    private function processUpdateList($updateProductList, $targetMarketplaceId, $referenceMarketplaceId, $toMarketplace): void
    {
//        $message = new ProductListingMessage(
//            'update_list',
//            $targetMarketplaceId,
//            $referenceMarketplaceId,
//            'admin',
//            $updateProductList,
//            [],
//            1,
//            'test',
//            $this->logger
//        );
//        $stamps = [new TransportNamesStamp(['ciceksepeti'])];
//        $this->bus->dispatch($message, $stamps);
//        $this->logger->info("[" . __METHOD__ . "] ✅ UpdateProductsList sent to Ciceksepeti Queue");
    }

    private function resolveTransportName(string $toMarketplace)
    {
        $knownTransports = ['hepsiburada', 'trendyol', 'n11', 'amazon', 'ciceksepeti'];
        $normalized = strtolower($toMarketplace);
        foreach ($knownTransports as $known) {
            if (str_starts_with($normalized, $known)) {
                return $known;
            }
        }
        return null;
    }

    private function getFromMarketplaceVariantIds(string $marketplace): array | null
    {
        $variantProductQuerySql = "SELECT oo_id FROM object_query_varyantproduct WHERE marketplace__id = :marketplace_id";
        $variantProductIds = Utility::fetchFromSql($variantProductQuerySql, ['marketplace_id' => $this->marketplaceConfig[$marketplace]]);
        if (!is_array($variantProductIds) || empty($variantProductIds)) {
            echo "Marketplace $marketplace variant product ids not found\n";
            $this->logger->error("[" . __METHOD__ . "] ❌ From Marketplace $marketplace variant product ids not found");
            return null;
        }
        $count = count($variantProductIds);
        echo "Marketplace $marketplace variant product ids found: $count\n";
        $this->logger->info("[" . __METHOD__ . "] ✅ From Marketplace $marketplace variant product ids found: $count");
        return array_column($variantProductIds, 'oo_id');
    }

    private function getTargetMarketplaceVariantProduct(string $marketplace, string $iwasku): VariantProduct | null
    {
        $sql = "SELECT oo_id FROM object_query_varyantproduct WHERE sellerSku = :seller_sku AND marketplace__id = :marketplace_id";
        $targetMarketplaceVariantProductIds = Utility::fetchFromSql($sql, ['seller_sku' => $iwasku, 'marketplace_id' => $this->marketplaceConfig[$marketplace]]);
        if (!is_array($targetMarketplaceVariantProductIds) || empty($targetMarketplaceVariantProductIds) || !isset($targetMarketplaceVariantProductIds[0]['oo_id'])) {
            $this->logger->info("[" . __METHOD__ . "] 🆕 Target Marketplace $marketplace variant product not found for iwasku: $iwasku, adding to list for creation. ");
            return null;
        }
        if (count($targetMarketplaceVariantProductIds) > 1) {
            $this->logger->error("[" . __METHOD__ . "] ❌ Target Marketplace $marketplace variant product count is more than 1 for iwasku: $iwasku ");
        }
        $targetMarketplaceVariantProductId = $targetMarketplaceVariantProductIds[0]['oo_id'] ?? '';
        $targetMarketplaceVariantProduct = VariantProduct::getById($targetMarketplaceVariantProductId);
        if (!$targetMarketplaceVariantProduct instanceof VariantProduct) {
            $this->logger->error("[" . __METHOD__ . "] ❌ Target Marketplace $marketplace variant product not found for iwasku: $iwasku ");
            return null;
        }
        $targetMarketplaceMainProduts = $targetMarketplaceVariantProduct->getMainProduct();
        if (!is_array($targetMarketplaceMainProduts) || empty($targetMarketplaceMainProduts) || !$targetMarketplaceMainProduts[0] instanceof Product) {
            $this->logger->error("[" . __METHOD__ . "] ❌ Target Marketplace $marketplace main product not found for iwasku: $iwasku ");
            return null;
        }
        return $targetMarketplaceVariantProduct;
    }

}