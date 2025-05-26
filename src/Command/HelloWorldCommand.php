<?php

namespace App\Command;

use App\Connector\Gemini\GeminiConnector;
use App\Message\CiceksepetiCategoryUpdateMessage;
use App\Message\TestMessage;
use App\Model\DataObject\Marketplace;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Doctrine\DBAL\Exception;
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
        $cfwTrSql = "SELECT oo_id FROM object_query_varyantproduct WHERE marketplace__id = :marketplace_id";
        $cfwTrVariantProductsIds = Utility::fetchFromSql($cfwTrSql, ['marketplace_id' => 84124]);
        if (!is_array($cfwTrVariantProductsIds) || empty($cfwTrVariantProductsIds)) {
            echo "No Shopify products found for Ciceksepeti sync.\n";
        }
        foreach ($cfwTrVariantProductsIds as $cfwTrVariantProductsId) {
            $shopifyProduct = VariantProduct::getById($cfwTrVariantProductsId['oo_id']);
            if (!$shopifyProduct instanceof VariantProduct) {
                echo "Invalid Shopify product ID: " . $cfwTrVariantProductsId['oo_id'] . ", skipping...";
                continue;
            }
            $mainProducts = $shopifyProduct->getMainProduct();
            if (!is_array($mainProducts) || empty($mainProducts) || !$mainProducts[0] instanceof Product) {
                echo "No main product found for Shopify product ID: " . $cfwTrVariantProductsId['oo_id'] . "\n";
                continue;
            }
            $mainProduct = $mainProducts[0];
            $this->dimTest($mainProduct->getVariationSize());
//            $result = $this->findBestAttributeMatch(2000361, $mainProduct->getVariationSize(), true);
//            echo $mainProduct->getIwasku() . " - " . $mainProduct->getVariationSize() . " -----> ";
//            print_r($result);
//            echo "\n";

        }

        return Command::SUCCESS;
    }

    private array $allValues = [];

    private function loadAllValues(): void {
        if (empty($this->allValues)) {
            $sql = "SELECT name FROM iwa_ciceksepeti_category_attributes_values WHERE attribute_id = :attribute_id";
            $result = Utility::fetchFromSql($sql, ['attribute_id' => 2000361]);
            $this->allValues = $result;
        }
    }

    private function fetchMatch($valueMain): ?string {
        $this->loadAllValues();
        foreach ($this->allValues as $value) {
            if ($value['name'] === $valueMain) {
                return $value['name'];
            }
        }
        return null;
    }

    private function dimTest($valueMain)
    {
        $valueMain = trim($valueMain);
        if ($result = $this->fetchMatch($valueMain)) {
            echo "$valueMain -> DOĞRUDAN ESLESME BULUNDU\n";
            return;
        }
        if (strpos($valueMain, '-') !== false) {
            $firstPart = explode('-', $valueMain)[0];
            if ($result = $this->fetchMatch($firstPart)) {
                echo "$valueMain -> $firstPart ESLESME BULUNDU\n";
                return;
            }
        }
        if (preg_match('/^(\d+)(x(\d+))?(x(\d+))?cm$/i', strtolower($valueMain), $matches)) {
            $dim1 = isset($matches[1]) ? (int)$matches[1] : null;
            $dim2 = isset($matches[3]) ? (int)$matches[3] : null;
            $dim3 = isset($matches[5]) ? (int)$matches[5] : null;
            for ($i = 0; $i < 25; $i++) {
                $d1 = $dim1 - $i;
                if ($dim2 === null) {
                    $tryValue = "{$d1}cm";
                    if ($result = $this->fetchMatch($tryValue)) {
                        echo "$valueMain -> $tryValue ESLESME BULUNDU\n";
                        return;
                    }
                    continue;
                }
                for ($j = 0; $j < 25; $j++) {
                    $d2 = $dim2 - $j;
                    if ($dim3 === null) {
                        $tryValue = "{$d1}x{$d2}cm";
                        if ($result = $this->fetchMatch($tryValue)) {
                            echo "$valueMain -> $tryValue ESLESME BULUNDU\n";
                            return;
                        }
                        continue;
                    }
                    for ($k = 0; $k < 25; $k++) {
                        $d3 = $dim3 - $k;
                        $tryValue = "{$d1}x{$d2}x{$d3}cm";
                        if ($result = $this->fetchMatch($tryValue)) {
                            echo "$valueMain -> $tryValue ESLESME BULUNDU\n";
                            return;
                        }
                    }
                }
            }
        }
        echo "$valueMain -> ESLESME BULUNAMADI\n";
    }

}
