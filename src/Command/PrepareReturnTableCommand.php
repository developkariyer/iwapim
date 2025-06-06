<?php

namespace App\Command;

use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Product;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;

#[AsCommand(
    name: 'app:prepare-return-table',
    description: 'Prepare returnItems table from returns table',
)]

class PrepareReturnTableCommand extends AbstractCommand
{
    private array $marketplaceListWithIds = [];

    private string $transferSqlfilePath = PIMCORE_PROJECT_ROOT . '/src/SQL/ReturnTable/Transfer/';

    protected function configure(): void
    {
        $this
            ->addOption('transfer',null, InputOption::VALUE_NONE, 'Transfer iwa_marketplace_returns to iwa_marketplace_returns_line_items')
            ->addOption('extraColumns',null, InputOption::VALUE_NONE, 'Insert extra columns')
        ;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if($input->getOption('transfer')) {
            $this->transferReturns();
        }
        if($input->getOption('extraColumns')) {
            $this->setMarketplaceKey();
        }
        return Command::SUCCESS;
    }

    protected function marketplaceList(): void
    {
        $marketplaceList = Marketplace::getMarketplaceList();
        foreach ($marketplaceList as $marketplace) {
            $this->marketplaceListWithIds[$marketplace->getId()] = $marketplace->getMarketplaceType();
        }
    }

    /**
     * @throws Exception
     */
    protected function transferReturns(): void
    {
        if (empty($this->marketplaceListWithIds)) {
            $this->marketplaceList();
        }
        $marketplaceIds = Utility::fetchFromSqlFile($this->transferSqlfilePath . 'selectMarketplaceIds.sql');
        $fileNames = [
            'Bol.com'  =>  'iwa_marketplace_returns_transfer_bolcom.sql',
            'Trendyol' =>  'iwa_marketplace_returns_transfer_trendyol.sql',
            'Wallmart' =>  'iwa_marketplace_returns_transfer_wallmart.sql',
            'Takealot' =>  'iwa_marketplace_returns_transfer_takealot.sql',
            'Shopify'  =>  'iwa_marketplace_returns_transfer_shopify.sql',
            'Amazon'   =>  'iwa_marketplace_returns_transfer_amazon.sql',
            'Etsy'     =>  'iwa_marketplace_returns_transfer_etsy.sql',
            'Wayfair'  =>  'iwa_marketplace_returns_transfer_wayfair.sql'
        ];
        foreach ($marketplaceIds as $marketplaceId) {
            $id = $marketplaceId['marketplace_id'];
            if (isset($this->marketplaceListWithIds[$id])) {
                $marketplaceType = $this->marketplaceListWithIds[$id];
                echo "Marketplace ID: $id - Type: $marketplaceType\n";
                if (isset($fileNames[$marketplaceType])) {
                        Utility::executeSqlFile($this->transferSqlfilePath . $fileNames[$marketplaceType], [
                            'marketPlaceId' => $id,
                            'marketplaceType' => $marketplaceType
                        ]);
                        echo "Executed: $marketplaceType\n";
                }
                echo "Completed: $marketplaceType\n";
            }
        }
    }

    public function setMarketplaceKey(): void
    {
        $selectMarketplaceKeySql = "
            SELECT
                DISTINCT marketplace_id
            FROM
                iwa_marketplace_returns_line_items
            WHERE
                marketplace_id IS NOT NULL";

        $updateMarketplaceKeySql = "
            UPDATE iwa_marketplace_returns_line_items
            SET marketplace_key = :marketplaceKey
            WHERE marketplace_id = :marketplaceId;";

        $values = Utility::fetchFromSql($selectMarketplaceKeySql);
        foreach ($values as $row) {
            $id = $row['marketplace_id'];
            $marketplace = Marketplace::getById($id);
            if ($marketplace) {
                $marketplaceKey = $marketplace->getKey();
                Utility::executeSql($updateMarketplaceKeySql, [
                    'marketplaceKey' => $marketplaceKey,
                    'marketplaceId' => $id
                ]);
            } else {
                echo "Marketplace not found for ID: $id\n";
            }
        }
    }
}