<?php

namespace App\Command;

use App\Message\CiceksepetiCategoryUpdateMessage;
use App\Message\TestMessage;
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
        $directory = PIMCORE_PROJECT_ROOT. "/tmp/marketplaces/Ciceksepeti";
        $files = array_filter(scandir($directory), function ($file) use ($directory) {
            return is_file($directory . DIRECTORY_SEPARATOR . $file) && str_starts_with($file, 'CREATE_LISTING_');
        });

        foreach ($files as $fileName) {
//            $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;
//            $content = file_get_contents($filePath);
//            $json = json_decode($content, true);
//            $this->test($json);
            foreach ($files as $fileName) {
                $parts = explode('_', $fileName, 3);
                if (isset($parts[2])) {
                    $idWithJson = $parts[2];
                    $id = pathinfo($idWithJson, PATHINFO_FILENAME);
                    echo $id . PHP_EOL;
                }
            }
        }
        return Command::SUCCESS;
    }

    public function test($json)
    {
        if (empty($json['response'])) {
            return;
        }
        $batchId = $json['response']['batchRequestResult']['batchId'];
        $items = $json['response']['batchRequestResult']['items'];
        $result = [];
        foreach ($items as $item) {
            $createdDate = $item['lastModificationDate'];
            $mainProduct = $item['data']['mainProductCode'] ?? null;
            $status = $item['status'] ?? null;
            $iwasku = $item['data']['stockCode'] ?? null;
            $failureReasons = [];
            if (!empty($item['failureReasons'])) {
                foreach ($item['failureReasons'] as $failureReason) {
                    $failureReasons[] = [
                        'code' => $failureReason['code'] ?? '',
                        'message' => $failureReason['message'] ?? ''
                    ];
                }
            }
            $result[] = [
                'batchId' => $batchId,
                'createdDate' => $createdDate,
                'mainProduct' => $mainProduct,
                'iwasku' => $iwasku,
                'status' => $status,
                'failureReasons' => $failureReasons
            ];
        }
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        // return $result; // dilersen diziyi ham olarak da döndürebilirsin
    }
}
