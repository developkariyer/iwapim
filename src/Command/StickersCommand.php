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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Model\DataObject\GroupProduct;

#[AsCommand(
    name: 'app:stickers',
    description: 'Generate missing stickers'
)]

class StickersCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->addOption('generateEu', null, InputOption::VALUE_NONE, 'Generate missing stickers')
            ->addOption('generateIwasku', null, InputOption::VALUE_NONE, 'Generate missing stickers')
            ->addOption('generateEuFnsku', null, InputOption::VALUE_NONE, 'Generate missing stickers')
            ->addOption('generateEuFnskuByGroupId', null, InputOption::VALUE_NONE, 'Generate missing stickers')
        ;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('generateEu')) {
            $this->generateEuSticker();
        }

        if ($input->getOption('generateIwasku')) {
            $this->generateIwaskuBarcodeSticker();
        }

        if ($input->getOption('generateEuFnsku')) {
            $this->generateEuFnskuSticker();
        }

        if ($input->getOption('generateEuFnskuByGroupId')) {
            $this->generateEuFnskuStickerByGroupId(271263);
        }

        return Command::SUCCESS;
    }

    public function generateEuSticker()
    {
        $db = Db::get();
        $gproduct = new GroupProduct\Listing();
        $result = $gproduct->load();
        foreach ($result as $item) {
            echo "Group: ".$item->getKey();
            $products = $db->fetchAllAssociative("SELECT dest_id FROM object_relations_gproduct WHERE src_id = ? AND fieldname = 'products'", [$item->getId()]);
            echo " Products: ".count($products)."\n";
            foreach ($products as $product) {
                $details = $db->fetchAssociative("SELECT * FROM object_store_product WHERE oo_id = ? LIMIT 1", [$product['dest_id']]);
                $stickerId = $db->fetchOne("SELECT dest_id FROM object_relations_product WHERE src_id = ? AND type='asset' AND fieldname='sticker4x6eu'", [$product['dest_id']]);
                echo "  Product: ".$details['oo_id']." Sticker: ".$stickerId." ". "\n";
                if (!$stickerId) {
                    $productObject = Product::getById($product['dest_id']);
                    if (!$productObject) {
                        echo " product not found\n";
                        continue;
                    }
                    echo " generating ";
                    $sticker = $productObject->checkSticker4x6eu();
                } else {
                    $sticker = Asset::getById($stickerId);
                }
                if ($sticker) {
                    echo $sticker->getFullPath();
                    echo "\n";
                }
            }
        }
    }

    public function generateIwaskuBarcodeSticker()
    {
        $db = Db::get();
        $gproduct = new GroupProduct\Listing();
        $result = $gproduct->load();
        foreach ($result as $item) {
            echo "Group: ".$item->getKey();
            $products = $db->fetchAllAssociative("SELECT dest_id FROM object_relations_gproduct WHERE src_id = ? AND fieldname = 'products'", [$item->getId()]);
            echo " Products: ".count($products)."\n";
            foreach ($products as $product) {
                $details = $db->fetchAssociative("SELECT * FROM object_store_product WHERE oo_id = ? LIMIT 1", [$product['dest_id']]);
                $stickerId = $db->fetchOne("SELECT dest_id FROM object_relations_product WHERE src_id = ? AND type='asset' AND fieldname='sticker4x6iwasku'", [$product['dest_id']]);
                echo "  Product: ".$details['oo_id']." Sticker: ".$stickerId." ". "\n";
                if (!$stickerId) {
                    $productObject = Product::getById($product['dest_id']);
                    if (!$productObject) {
                        echo " product not found\n";
                        continue;
                    }
                    echo " generating ";
                    $sticker = $productObject->checkSticker4x6iwasku();
                } else {
                    $sticker = Asset::getById($stickerId);
                }
                if ($sticker) {
                    echo $sticker->getFullPath();
                    echo "\n";
                }
            }
        }

    }

    public function generateEuFnskuStickerByGroupId($groupId)
    {
        $db = Db::get();
        //$gproduct = new GroupProduct\Listing();
        $gproduct = GroupProduct::getById($groupId);
        $result = $gproduct->load();
        foreach ($result as $item) {
            echo "Group: ".$item->getKey();
            $products = $db->fetchAllAssociative("SELECT dest_id FROM object_relations_gproduct WHERE src_id = ? AND fieldname = 'products'", [$item->getId()]);
            echo " Products: ".count($products)."\n";
            foreach ($products as $product) {
                $stickerIds = $db->fetchAssociative("SELECT dest_id FROM object_relations_product WHERE src_id = ? AND type='asset' AND fieldname='stickerFnsku'", [$product['dest_id']]);
                foreach ($stickerIds as $stickerId) {
                    if (!$stickerId) {
                        $productObject = Product::getById($product['dest_id']);
                        if (!$productObject) {
                            echo " product not found\n";
                            continue;
                        }
                        echo " generating ";
                        $sticker = $productObject->checkStickerFnsku();
                    } else {
                        $sticker = Asset::getById($stickerId);
                    }
                    if ($sticker) {
                        echo $sticker->getFullPath();
                        echo "\n";
                    }
                }
            }
        }

    }

    public function generateEuFnskuSticker()
    {
        $db = Db::get();
        $gproduct = new GroupProduct\Listing();
        $result = $gproduct->load();
        foreach ($result as $item) {
            echo "Group: ".$item->getKey();
            $products = $db->fetchAllAssociative("SELECT dest_id FROM object_relations_gproduct WHERE src_id = ? AND fieldname = 'products'", [$item->getId()]);
            echo " Products: ".count($products)."\n";
            foreach ($products as $product) {
                $stickerIds = $db->fetchAssociative("SELECT dest_id FROM object_relations_product WHERE src_id = ? AND type='asset' AND fieldname='stickerFnsku'", [$product['dest_id']]);
                foreach ($stickerIds as $stickerId) {
                    if (!$stickerId) {
                        $productObject = Product::getById($product['dest_id']);
                        if (!$productObject) {
                            echo " product not found\n";
                            continue;
                        }
                        echo " generating ";
                        $sticker = $productObject->checkStickerFnsku();
                    } else {
                        $sticker = Asset::getById($stickerId);
                    }
                    if ($sticker) {
                        echo $sticker->getFullPath();
                        echo "\n";
                    }
                }
            }
        }
    }

}

