<?php

namespace App\Connector\Wisersell;

use App\Connector\Wisersell\Connector;

class ListingSyncService
{
    private $connector;

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

}