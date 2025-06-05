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
//        match ($actionType) {
//            'list' => $this->processNewListing($message),
//            'update_list' => $this->processUpdateListing($message),
//            default => throw new \InvalidArgumentException("Unknown Action Type: $actionType")
//        };
    }
}
