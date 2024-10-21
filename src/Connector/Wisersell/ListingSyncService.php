<?php

namespace App\Connector\Wisersell;

use App\Connector\Wisersell\Connector;

class ListingSyncService
{
    protected $connector;
    public $wisersellListings = [];
    public $pimListings = [];

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    public function loadWisersell($force = false)
    {
        if (!$force && !empty($this->wisersellListings)) {
            return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/listings.json');
        }
        $this->wisersellListings = json_decode(Utility::getCustomCache('listings.json', PIMCORE_PROJECT_ROOT . '/tmp/wisersell'), true);
        if (!$force && !empty($this->wisersellListings)) {
            return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/listings.json');
        }
        $this->wisersellListings = $this->search([]);
        Utility::setCustomCache('listings.json', PIMCORE_PROJECT_ROOT . '/tmp/wisersell', json_encode($this->wisersellListings));
        return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/listings.json');
    }

    public function loadPim($force = false) 
    {
        if (!$force && !empty($this->pimListings)) {
            return;
        }
        $db = \Pimcore\Db::get();
        $this->pimListings = [];
        $listings = $db->fetchAllAssociative('SELECT oo_id, wisersellVariantCode, calculatedWisersellCode FROM object_varyantproduct WHERE published = 1');
        foreach ($listings as $listing) {
            if (strlen($listing['calculatedWisersellCode'])<1) {
                continue;
            }
            $this->pimListings[$listing['iwasku']] = $listing['oo_id'];
        }
    }


    public function status()
    {
        $cacheExpire = $this->load();
        return [
            'wisersell' => count($this->wisersellListings),
            'pim' => count($this->pimListings),
            'expire' => 86400-$cacheExpire
        ];
    }

    public function load($force = false)
    {
        $this->loadPim($force);
        return $this->loadWisersell($force);
    }

    public function search($params)
    {
    }

}