<?php
namespace App\MessageHandler;
use App\Message\ProductListingMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(fromTransport: 'takealot')]
class TakealotListingHandler
{
    public function __invoke(ProductListingMessage $message)
    {
        $messageData = [
            'traceId' => $message->getTraceId(),
            'actionType' => $message->getActionType(),
            'productId' => $message->getProductId(),
            'marketplaceId' => $message->getMarketplaceId(),
            'userId' => $message->getUserName(),
            'variantIds' => $message->getVariantIds(),
            'payload' => $message->getPayload(),
            'priority' => $message->getPriority(),
            'targetAccountKey' => $message->getTargetAccountKey(),
            'createdAt' => $message->getCreatedAt()->format(\DateTimeInterface::ISO8601),
        ];

        $jsonOutput = json_encode($messageData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        echo "Takealot Mesaj İşlendi (JSON):\n";
        echo $jsonOutput . "\n";

    }
}