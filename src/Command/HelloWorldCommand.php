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
        $sql = "SELECT oo_id FROM object_query_varyantproduct WHERE marketplace__id = :marketplace_id";
        $variantProductIds = Utility::fetchFromSql($sql, ['marketplace_id' => 84124]);

        $processedMainProductIds = [];

        foreach ($variantProductIds as $variantProductId) {
            $variantProduct = VariantProduct::getById($variantProductId['oo_id']);
            if (!$variantProduct instanceof VariantProduct) {
                continue;
            }
            $mainProducts = $variantProduct->getMainProduct();
            if (empty($mainProducts)) {
                continue;
            }
            $mainProduct = $mainProducts[0];
            if (!$mainProduct instanceof Product) {
                continue;
            }
            $mainProductId = $mainProduct->getId();
            if (in_array($mainProductId, $processedMainProductIds)) {
                continue;
            }
            $processedMainProductIds[] = $mainProductId;
            echo "*********************************************************************************\n";
            echo $mainProduct->getIwasku() . "\n";
            $sizeLabelFromParent = $this->getSizeLabelFromParent($mainProduct);
            print_r($sizeLabelFromParent);
            echo "*********************************************************************************\n";
        }

        return Command::SUCCESS;
    }

    private function getSizeLabelFromParent($referenceMarketplaceMainProduct)
    {
        $parentProduct = $referenceMarketplaceMainProduct->getParent();
        if (!$parentProduct instanceof \App\Model\DataObject\Product) {
            return;
        }
        $variationSizeList = $parentProduct->getVariationSizeList();
        $lines = explode("\n", trim($variationSizeList));
        $parsed = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (preg_match('/^(XS|S|M|L|XL|2XL|3XL|4XL|5XL)$/i', $line, $matches)) {
                $label = strtoupper($matches[1]);
                $parsed[] = ['original' => $line, 'label' => $label, 'value' => $label];
            }
            elseif (preg_match('/^([0-9]*[A-Z]{1,3})[-\s]+(.+)$/i', $line, $matches)) {
                $label = strtoupper($matches[1]);
                $value = trim($matches[2]);
                $parsed[] = ['original' => $line, 'label' => $label, 'value' => $value];
            }
            else {
                $parsed[] = ['original' => $line, 'label' => null, 'value' => $line];
            }
        }
        $autoLabels = ['M', 'L', 'XL', '2XL', '3XL', '4XL'];
        $autoIndex = 0;
        foreach ($parsed as &$item) {
            if ($item['label'] === null) {
                $item['label'] = $autoLabels[$autoIndex] ?? ('+' . end($autoLabels));
                $autoIndex++;
            }
        }
        return $parsed;
    }

    private function pazaramaTrendyolMatch()
    {
        $pazaramaMainProductYesCount = 0;
        $pazaramaMainProductNoCount = 0;
        $trendyolPazaramaMatchCount = 0;
        $trendyolPazaramaNoMatchCount = 0;
        $pazaramaVariantProductSql = "SELECT oo_id FROM object_query_varyantproduct WHERE marketplace__id = :marketplace_id";
        $trendyolControlSql = "SELECT oo_id FROM object_query_varyantproduct WHERE sellerSku = :seller_sku and marketplace__id = :marketplace_id";
        $pazaramaVariantProductIds = Utility::fetchFromSql($pazaramaVariantProductSql, ['marketplace_id' => 279708]);
        foreach ($pazaramaVariantProductIds as $pazaramaVariantProductId) {
            $variantProductPazarama = VariantProduct::getById($pazaramaVariantProductId['oo_id']);
            if (!$variantProductPazarama instanceof VariantProduct) {
                echo "Invalid variantProductId:$pazaramaVariantProductId\n";
                continue;
            }
            $mainProductPazarama = $variantProductPazarama->getMainProduct();
            if (empty($mainProductPazarama)){
                echo "Main product not found" . $variantProductPazarama->getSellerSku() . "\n";
//                $pazaramaMainProductNoCount++;
//               echo "Main product not found Pazarama\n";
//               $sellerSkuPazarama = $variantProductPazarama->getSellerSku();
//               echo "Pazarama Seller Sku => " . $sellerSkuPazarama . "\n";
//               $trendyolVariantProductIds = Utility::fetchFromSql($trendyolControlSql, ['seller_sku' => $sellerSkuPazarama,'marketplace_id' => 169698]);
//               if (empty($trendyolVariantProductIds)) {
//                   echo "Trendyol variant product not found : " . $sellerSkuPazarama . "\n";
//                   $trendyolPazaramaNoMatchCount++;
//                   continue;
//               }
//               $variantProductTrendyol = VariantProduct::getById($trendyolVariantProductIds[0]['oo_id']);
//               if (!$variantProductTrendyol instanceof VariantProduct) {
//                   echo "Invalid variantProductId:$trendyolVariantProductIds[0]\n";
//                   continue;
//               }
//               $trendyolMainProducts = $variantProductTrendyol->getMainProduct();
//               if (empty($trendyolMainProducts)) {
//                   echo "Trendyol main product not found\n";
//                   continue;
//               }
//               $trendyolMainProduct = $trendyolMainProducts[0];
//               if (!$trendyolMainProduct instanceof Product) {
//                   echo "Invalid trendyol main product\n";
//                   continue;
//               }
//               echo $trendyolMainProduct->getIwasku() ."\n";
//                $trendyolPazaramaMatchCount++;
//                $result = $trendyolMainProduct->addVariant($variantProductPazarama);
//                if (!$result) {
//                    echo "Error adding variant to trendyol main product\n";
//                }
//                echo "Added variant to trendyol main product" . $result . "\n";
            }
//            else {
//                $pazaramaMainProductYesCount++;
//                echo "Main product found Pazarama \n";
//            }
        }
//        echo "Pazarama Main Product Yes: " . $pazaramaMainProductYesCount . " Pazarama Main Product No: " . $pazaramaMainProductNoCount . "\n";
//        echo "Trendyol Match Pazarama: " . $trendyolPazaramaMatchCount . " Trendyol No Match Pazarama: " . $trendyolPazaramaNoMatchCount . "\n";
    }

}
