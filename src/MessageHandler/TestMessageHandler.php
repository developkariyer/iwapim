<?php
#[AsMessageHandler]
class TestMessageHandler
{
    public function __invoke(TestMessage $message)
    {
        echo "Mesaj işlendi: " . $message->content . "\n";
    }
}