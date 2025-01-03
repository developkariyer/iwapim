<?php

namespace App\Command;

use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Product;
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
    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $db = Db::get();
        $stickers = [];
        $products = $db->fetchAllAssociative("SELECT dest_id FROM object_relations_gproduct WHERE src_id = ? AND fieldname = 'products'", [249889]);
        foreach ($products as $product) {
            $details = $db->fetchAssociative("SELECT * FROM object_store_product WHERE oo_id = ? LIMIT 1", [$product['dest_id']]);
            $stickerId = $db->fetchOne("SELECT dest_id FROM object_relations_product WHERE src_id = ? AND type='asset' AND fieldname='sticker4x6eu'", [$product['dest_id']]);
            if (!$stickerId) {
                $productObject = Product::getById($product['dest_id']);
                if (!$productObject) {
                    continue;
                }
                $sticker = $productObject->checkSticker4x6eu();
            } else {
                $sticker = Asset::getById($stickerId);
            }
            if ($sticker) {
                $stickerPath = $sticker->getFullPath();
            }
            $stickers[] = [
                'iwasku' => $details['iwasku'],
                'product_name' => $details['Key'],
                'sticker_link' => $stickerPath ?? '',
                'product_code' => $details['productCode'] ?? '',
                'category' => $details['productCategory'] ?? '',
                'image_link' => $details['imageUrl'] ?? '',
                'attributes' => $details['variationSize'] . ' ' . $details['variationColor']
            ];

        }
        print_r($stickers);
        // Output "Hello, World!" as green text
       // $this->writeInfo("Hello, World!", $output);

        // Return success status code
        return Command::SUCCESS;
    }
}
