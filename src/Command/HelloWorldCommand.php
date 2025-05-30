<?php

namespace App\Command;

use App\Connector\Gemini\GeminiConnector;
use App\Connector\Marketplace\CiceksepetiConnector;
use App\Message\CiceksepetiCategoryUpdateMessage;
use App\Message\TestMessage;
use App\Model\DataObject\Marketplace;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Doctrine\DBAL\Exception;
use LesserPHP\Utils\Util;
use phpseclib3\File\ASN1\Maps\AttributeValue;
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
    name: 'app:hello-world',
    description: 'Outputs Hello, World!'
)]
class HelloWorldCommand extends AbstractCommand
{
    public function __construct(private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pazaramaVariantProductSql = "SELECT oo_id FROM object_query_varyantproduct WHERE marketplace__id = :marketplace_id";
        $trendyolControlSql = "SELECT oo_id FROM object_query_varyantproduct WHERE sellerSku = :seller_sku and marketplace__id = :marketplace_id";
        $pazaramaVariantProductIds = Utility::fetchFromSql($pazaramaVariantProductSql, ['marketplace_id' => 284396]);
        foreach ($pazaramaVariantProductIds as $pazaramaVariantProductId) {
            $variantProductPazarama = VariantProduct::getById($pazaramaVariantProductId['oo_id']);
            if (!$variantProductPazarama instanceof VariantProduct) {
                echo "Invalid variantProductId:$pazaramaVariantProductId\n";
                continue;
            }
            $mainProductPazarama = $variantProductPazarama->getMainProduct();
            if (empty($mainProductPazarama)){
               echo "Main product not found\n";
               $sellerSkuPazarama = $variantProductPazarama->getSellerSku();
               $trendyolVariantProductIds = Utility::fetchFromSql($trendyolControlSql, ['seller_sku' => $sellerSkuPazarama,'marketplace_id' => 169698]);
               if (empty($trendyolVariantProduct)) {
                   echo "Trendyol variant product not found\n";
                   continue;
               }
               $variantProductTrendyol = VariantProduct::getById($trendyolVariantProductIds[0]['oo_id']);
               if (!$variantProductTrendyol instanceof VariantProduct) {
                   echo "Invalid variantProductId:$trendyolVariantProductIds[0]\n";
                   continue;
               }
               echo "Pazarama Seller Sku => " . $variantProductPazarama->getSellerSku() . " Trendyol seller Sku: " . $variantProductTrendyol->getSellerSku() . "\n";


            }
            else {
                echo "Main product found \n";
            }
        }




        return Command::SUCCESS;
    }

}
