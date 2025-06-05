<?php
namespace App\MessageHandler;


use App\Message\ProductListingMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Connector\Gemini\GeminiConnector;
use App\Connector\Marketplace\HepsiburadaConnector;
use App\Model\DataObject\Marketplace;
use App\Model\DataObject\Product;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Exception;
use Symfony\Component\HttpClient\HttpClient;
use App\MessageHandler\ListingHelperService;
use Psr\Log\LoggerInterface;
use App\Logger\LoggerFactory;

#[AsMessageHandler(fromTransport: 'hepsiburada')]
class HepsiburadaListingHandler
{
    private LoggerInterface $logger;

    public function __construct(ListingHelperService $listingHelperService)
    {
        $this->listingHelper = $listingHelperService;
    }

    public function __invoke(ProductListingMessage $message)
    {
        if (method_exists($message, 'getLogger') && $message->getLogger() instanceof LoggerInterface) {
            $this->logger = $message->getLogger();
        }
        echo "Hepsiburada Listing Handler\n";
        $this->logger->info("[" . __METHOD__ . "] 🚀 Hepsiburada Listing Handler Started");
        $actionType = $message->getActionType();
        echo "action type: $actionType\n";
        $this->logger->info("[" . __METHOD__ . "] ✅ Action Type: $actionType ");
        match ($actionType) {
            'list' => $this->processNewListing($message),
            //'update_list' => $this->processUpdateListing($message),
            default => throw new \InvalidArgumentException("Unknown Action Type: $actionType")
        };
    }

    private function processNewListing($message)
    {
        $this->logger->info("[" . __METHOD__ . "] ✅ Processing New Listing ");
        $listingInfo = $this->listingHelper->getPimListingsInfo($message, $this->logger);
        $this->logger->info("[" . __METHOD__ . "] ✅ Pim Listings Info Fetched ");
        $categories = $this->getHepsiburadaCategoriesDetails();
        $this->logger->info("[" . __METHOD__ . "] ✅ Category Data Fetched ");
        $geminiFilledData = $this->geminiProcess($listingInfo, $categories);

    }

    private function geminiProcess($data, $categories)
    {
        $firstProduct = $data[0];
        $geminiData = [
            'title'       => $firstProduct['title'],
            'description' => $firstProduct['description'] ?? null,
            'categoryId'  => null,
            'variants'    => []
        ];
        foreach ($data as $product) {
            $geminiData['variants'][] = [
                'stockCode' => $product['stockCode'] ?? null,
                'color'     => $product['color'] ?? null,
            ];
        }
        $this->logger->info("[" . __METHOD__ . "] ✅ Gemini Data Created Variant Count: " . count($geminiData['variants']));
        $prompt = $this->generateListingPrompt(json_encode(['products' => $geminiData], JSON_UNESCAPED_UNICODE), $categories);
        $this->logger->info("[" . __METHOD__ . "] ✅ Gemini Api Send Data ");
        $geminiApiResult = GeminiConnector::chat($prompt, 'hepsiburada');
        $geminiResult = $this->parseGeminiResult($geminiApiResult);
        foreach ($data as &$product) {
            $product['geminiCategoryId']   = $geminiResult['categoryId'] ?? null;
            $product['geminiTitle']        = $geminiResult['title'] ?? null;
            $product['geminiDescription']  = $geminiResult['description'] ?? null;
            $product['geminiColor'] = null;
            foreach ($geminiResult['variants'] as $variant) {
                if ($variant['stockCode'] === $product['stockCode']) {
                    $product['geminiColor'] = $variant['color'];
                    break;
                }
            }
        }
        return $data;
    }

























    private function getHepsiburadaCategoriesDetails(): false|array|string
    {
        $categoryIdList = $this->getHepsiburadaListingCategoriesIdList();
        if (empty($categoryIdList)) {
            return [];
        }
        $inClause = implode(',', array_fill(0, count($categoryIdList), '?'));
        $sql = "SELECT * FROM iwa_hepsiburada_categories WHERE id IN ($inClause)";
        $categories = Utility::fetchFromSql($sql, $categoryIdList);
        if (empty($categories)) {
            return [];
        }
        return json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function getHepsiburadaListingCategoriesIdList(): array
    {
        $sql = "SELECT oo_id FROM `object_query_varyantproduct` WHERE marketplaceType = 'Hepsiburada'";
        $hepsiburadaVariantIds = Utility::fetchFromSql($sql);
        if (!is_array($hepsiburadaVariantIds) || empty($hepsiburadaVariantIds)) {
            return [];
        }
        $categoryIdList = [];
        foreach ($hepsiburadaVariantIds as $hepsiburadaVariantId) {
            $variantProduct = VariantProduct::getById($hepsiburadaVariantId['oo_id']);
            if (!$variantProduct instanceof VariantProduct) {
                continue;
            }
            $apiData = json_decode($variantProduct->jsonRead('apiResponseJson'), true);
            $categoryIdList[] = $apiData['attributes']['categoryId'];
        }
        return array_unique($categoryIdList);
    }

}
