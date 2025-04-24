<?php
namespace App\MessageHandler;


use App\Message\ProductListingMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TrendyolListingHandler
{
    public function __invoke(ProductListingMessage $message)
    {
        echo "Trendyol Mesaj işlendi: " . $message . "\n";
    }
}