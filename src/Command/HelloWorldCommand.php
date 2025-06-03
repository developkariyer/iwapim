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
        $product = Product::getById(269388);
        if (!$product instanceof Product) {
            return Command::FAILURE;
        }
        $name = $product->getName();
        $variationSize = $product->getVariationSize();
        $variationColor = $product->getVariationColor();
        $weight = $product->getInheritedField("packageWeight");
        $width = $product->getInheritedField("packageDimension1");
        $length = $product->getInheritedField("packageDimension2");
        $height = $product->getInheritedField("packageDimension3");
        echo "Name: $name\n";
        echo "Variation size: $variationSize\n";
        echo "Variation color: $variationColor\n";
        echo "Weight: $weight\n";
        echo "Width: $width\n";
        echo "Length: $length\n";
        echo "Height: $height\n";

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
