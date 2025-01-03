<?php

namespace App\Command;

use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\Model\Asset;
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
        $gproduct = new GroupProduct\Listing();
        $result = $gproduct->load();
        foreach ($result as $item) {
            $products = $db->fetchAllAssociative("SELECT dest_id FROM object_relations_gproduct WHERE src_id = ? AND fieldname = 'products'", [$item->getId()]);
            foreach ($products as $product) {
                $details = $db->fetchAllAssociative("SELECT * FROM object_store_product WHERE oo_id = ?", [$product['dest_id']]);
                $stickerId = $db->fetchOne("SELECT dest_id FROM object_relations_product WHERE src_id = ? AND type='asset' AND fieldname='sticker4x6'", [$product['dest_id']]);
                if (!$stickerId) {
                    // Burada sticker oluşturan kod çağrulmalı ve gelen id alınmalı
                    usleep(1);
                    // $stickerId = ??????
                }
                $sticker = Asset::getById($stickerId);
                echo $sticker->getFullPath();
                return Command::SUCCESS; // aksi halde çok fazla nesne çekmeye çalışacak
            }
        }


        // Output "Hello, World!" as green text
       // $this->writeInfo("Hello, World!", $output);

        // Return success status code
        return Command::SUCCESS;
    }
}
