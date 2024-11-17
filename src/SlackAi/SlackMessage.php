<?php

namespace App\SlackAi;

class SlackMessage
{
    private string $text;
    private string $responseUrl;
    private string $user;
    private ?string $threadTs;

    public function __construct(string $text, string $responseUrl, string $user, ?string $threadTs = null)
    {
        $this->text = $text;
        $this->responseUrl = $responseUrl;
        $this->user = $user;
        $this->threadTs = $threadTs;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getResponseUrl(): string
    {
        return $this->responseUrl;
    }

    public function getThreadTs(): ?string
    {
        return $this->threadTs;
    }

    public function getUser(): string
    {
        return $this->user;
    }
}
