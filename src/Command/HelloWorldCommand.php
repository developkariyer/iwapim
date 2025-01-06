<?php

namespace App\Command;

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
    private string $sqlPath = PIMCORE_PROJECT_ROOT . '/src/SQL/Sticker/';
    /**
     * @throws Exception
     * @throws DuplicateFullPathException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = 10;
        $offset = 0;
        $groupId = 1;
        $sql = "
            SELECT
                osp.iwasku,
                osp.name AS product_name,
                osp.productCode,
                osp.productCategory,
                osp.imageUrl,
                osp.variationSize,
                osp.variationColor,
                opr.dest_id AS sticker_id
            FROM object_relations_gproduct org
                     JOIN object_product osp ON osp.oo_id = org.dest_id
                     LEFT JOIN object_relations_product opr ON opr.src_id = osp.oo_id AND opr.type = 'asset' AND opr.fieldname = 'sticker4x6eu'
            WHERE org.src_id = :$groupId
            LIMIT $limit OFFSET $offset;";
        $products = Db::get()->fetchAllAssociative($sql);
        print_r($products);

        // Output "Hello, World!" as green text
       // $this->writeInfo("Hello, World!", $output);

        // Return success status code
        return Command::SUCCESS;
    }
}
