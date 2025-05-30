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
        $pazaramaVariantProductIds = Utility::fetchFromSql($pazaramaVariantProductSql, ['marketplace_id' => 284396]);
        foreach ($pazaramaVariantProductIds as $pazaramaVariantProductId) {
            $variantProductPazarama = VariantProduct::getById($pazaramaVariantProductId['oo_id']);
            $mainProductPazarama = $variantProductPazarama->getMainProduct();
            if (empty($mainProductPazarama)){
               echo "Main product not found\n";
            }
            else {
                echo $mainProductPazarama[0]->getProductIdentifier() . "\n";
            }
        }




        return Command::SUCCESS;
    }

}
