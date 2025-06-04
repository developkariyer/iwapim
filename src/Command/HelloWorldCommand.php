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
        /*
         *  "weight" => $product->getInheritedField("packageWeight"),
            "width" => $product->getInheritedField("packageDimension1"),
            "length" => $product->getInheritedField("packageDimension2"),
            "height" => $product->getInheritedField("packageDimension3"),
         * */
        $pageSize = 50;
        $offset = 0;
        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(false);
        $listingObject->setCondition("iwasku IS NOT NULL AND iwasku != '' AND packageWeight IS NULL");
        $listingObject->setLimit($pageSize);
        while (true) {
            $listingObject->setOffset($offset);
            $products = $listingObject->load();
            if (empty($products)) {
                break;
            }
            $offset += $pageSize;
            foreach ($products as $product) {
                if ($product->level() != 1 || !$product instanceof Product) {
                    continue;
                }
                $name = $product->getInheritedField("name");
                $iwasku = $product->getInheritedField("iwasku");
                $variationSize = $product->getVariationSize();
                $variationColor = $product->getVariationColor();
                $wsCategory = $product->getInheritedField("productCategory");
                $weight = $product->getInheritedField("packageWeight");
                $width = $product->getInheritedField("packageDimension1");
                $length = $product->getInheritedField("packageDimension2");
                $height = $product->getInheritedField("packageDimension3");
                $desi5000 = $product->getInheritedField("desi5000");
                echo "Name: $name, Iwasku: $iwasku, Variation size: $variationSize, Variation color: $variationColor, WS category: $wsCategory, Weight: $weight, Width: $width, Length: $length, Height: $height, Desi: $desi5000 \n";
            }
            echo "\nProcessed {$offset}\n";
        }





//        $product = Product::getById(269388);
//        if (!$product instanceof Product) {
//            return Command::FAILURE;
//        }
//        $name = $product->getInheritedField("name");
//        $variationSize = $product->getVariationSize();
//        $variationColor = $product->getVariationColor();
//        $wsCategory = $product->getInheritedField("productCategory");
//        $weight = $product->getInheritedField("packageWeight");
//        $width = $product->getInheritedField("packageDimension1");
//        $length = $product->getInheritedField("packageDimension2");
//        $height = $product->getInheritedField("packageDimension3");
//        $product->setPackageWeight(2);
//        $product->save();

        return Command::SUCCESS;
    }

    private function pazaramaTrendyolMatch()
    {
        $pazaramaMainProductYesCount = 0;
        $pazaramaMainProductNoCount = 0;
        $trendyolPazaramaMatchCount = 0;
        $trendyolPazaramaNoMatchCount = 0;
        $pazaramaVariantProductSql = "SELECT oo_id FROM object_query_varyantproduct WHERE marketplace__id = :marketplace_id";
        $trendyolControlSql = "SELECT oo_id FROM object_query_varyantproduct WHERE sellerSku = :seller_sku and marketplace__id = :marketplace_id";
        $pazaramaVariantProductIds = Utility::fetchFromSql($pazaramaVariantProductSql, ['marketplace_id' => 279709]);
        foreach ($pazaramaVariantProductIds as $pazaramaVariantProductId) {
            $variantProductPazarama = VariantProduct::getById($pazaramaVariantProductId['oo_id']);
            if (!$variantProductPazarama instanceof VariantProduct) {
                echo "Invalid variantProductId:$pazaramaVariantProductId\n";
                continue;
            }
            $mainProductPazarama = $variantProductPazarama->getMainProduct();
            if (empty($mainProductPazarama)){
                echo "Main product not found" . $variantProductPazarama->getSellerSku() . "\n";
                $pazaramaMainProductNoCount++;
                echo "Main product not found Pazarama\n";
                $sellerSkuPazarama = $variantProductPazarama->getSellerSku();
                echo "Pazarama Seller Sku => " . $sellerSkuPazarama . "\n";
                $trendyolVariantProductIds = Utility::fetchFromSql($trendyolControlSql, ['seller_sku' => $sellerSkuPazarama,'marketplace_id' => 169699]);
                if (empty($trendyolVariantProductIds)) {
                    echo "Trendyol variant product not found : " . $sellerSkuPazarama . "\n";
                    $trendyolPazaramaNoMatchCount++;
                    continue;
                }
                $variantProductTrendyol = VariantProduct::getById($trendyolVariantProductIds[0]['oo_id']);
                if (!$variantProductTrendyol instanceof VariantProduct) {
                    echo "Invalid variantProductId:$trendyolVariantProductIds[0]\n";
                    continue;
                }
                $trendyolMainProducts = $variantProductTrendyol->getMainProduct();
                if (empty($trendyolMainProducts)) {
                    echo "Trendyol main product not found\n";
                    continue;
                }
                $trendyolMainProduct = $trendyolMainProducts[0];
                if (!$trendyolMainProduct instanceof Product) {
                    echo "Invalid trendyol main product\n";
                    continue;
                }
                echo $trendyolMainProduct->getIwasku() ."\n";
                $trendyolPazaramaMatchCount++;
//                $result = $trendyolMainProduct->addVariant($variantProductPazarama);
//                if (!$result) {
//                    echo "Error adding variant to trendyol main product\n";
//                }
//                echo "Added variant to trendyol main product" . $result . "\n";
            }
            else {
                $pazaramaMainProductYesCount++;
                echo "Main product found Pazarama \n";
            }
        }
        echo "Pazarama Main Product Yes: " . $pazaramaMainProductYesCount . " Pazarama Main Product No: " . $pazaramaMainProductNoCount . "\n";
        echo "Trendyol Match Pazarama: " . $trendyolPazaramaMatchCount . " Trendyol No Match Pazarama: " . $trendyolPazaramaNoMatchCount . "\n";
    }

}
