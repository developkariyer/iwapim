<?php

namespace App\Message;

class CiceksepetiCategoryUpdateMessage
{
    private $marketplaceId;

    public function __construct(int $marketplaceId)
    {
        $this->marketplaceId = $marketplaceId;
    }

    public function getMarketplaceId(): int
    {
        return $this->marketplaceId;
    }

}
