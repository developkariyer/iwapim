<?php

namespace App\Message;

class TestMessage
{
    public $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }
}
