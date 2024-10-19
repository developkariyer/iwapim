<?php

namespace App\Connector\Wisersell;

use App\Connector\Wisersell\Connector;

class StoreSyncService
{
    private $connector;

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

}