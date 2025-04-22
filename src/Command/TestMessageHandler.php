<?php
namespace App\Command;

use App\Command\TestMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TestMessageHandler
{
    public function __invoke(TestMessage $message)
    {
        echo "Mesaj işlendi: " . $message->content . "\n";
    }
}