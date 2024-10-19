<?php

namespace App\Connector\Wisersell;

use Pimcore\Model\DataObject\Category;
use App\Connector\Wisersell\Connector;
use App\Utils\Utility;

class CategorySyncService
{
    protected $connector;
    protected $pimCategories;
    protected $wisersellCategories;

    public function __construct(Connector $connector)
    {   
        $this->connector = $connector;
    }

    public function loadPimCategories($force = false)
    {
        if (!$force && !empty($this->pimCategories)) {
            return;
        }
        $listingObject = new Category\Listing();
        $listingObject->setUnpublished(true);
        $categories = $listingObject->load();
        $this->pimCategories = [];
        foreach ($categories as $category) {
            $this->pimCategories[$category->getCategory()] = $category;
        }
    }

    public function loadWisersellCategories($force = false)
    {
        if (!$force && !empty($this->wisersellCategories)) {
            return;
        }
        $response = $this->connector->request(Connector::$apiUrl['category'], 'GET');
        if (empty($response)) {
            return;
        }
        $result = $response->toArray();
        $this->wisersellCategories = [];
        foreach ($result as $wisersellCategory) {
            if (isset($wisersellCategory['id']) && isset($wisersellCategory['name'])) {
                $this->wisersellCategories[$wisersellCategory['name']] = $wisersellCategory['id'];
            }
        }
    }

    public function load($force = false)
    {
        $this->loadPimCategories($force);
        $this->loadWisersellCategories($force);
    }

    public function addPimCategoryToWisersell($category)
    {
        if (!($category instanceof Category)) {
            return;
        }
        if (empty($this->wisersellCategories)) {
            $this->loadWisersellCategories();
        }
        if (isset($this->wisersellCategories[$category->getCategory()])) {
            $category->setWisersellCategoryId($this->wisersellCategories[$category->getCategory()]);
            $category->save();
            return;
        }
        $response = $this->connector->request(Connector::$apiUrl['category'], 'POST', '', [['name' => $category->getCategory()]]);
        if (empty($response)) {
            return;
        }
        $result = $response->toArray();
        foreach ($result as $wisersellCategory) {
            if (isset($wisersellCategory['id']) && isset($wisersellCategory['name']) && $wisersellCategory['name'] === $category->getCategory()) {
                $category->setWisersellCategoryId($wisersellCategory['id']);
                $category->save();
                $this->wisersellCategories[$wisersellCategory['name']] = $wisersellCategory['id'];
            }
        }
    }

    public function updateWisersellCategory($categoryId, $categoryName)
    {
        if (empty($this->wisersellCategories)) {
            $this->loadWisersellCategories();
        }
        if (isset($this->wisersellCategories[$categoryName]) && $this->wisersellCategories[$categoryName] == $categoryId) {
            return;
        }
        $response = $this->connector->request(Connector::$apiUrl['category'], 'PUT', "/{$categoryId}", ['name' => $categoryName]);
        if (empty($response)) {
            return null;
        }
        $wisersellCategory = $response->toArray();
        if (isset($wisersellCategory['id']) && isset($wisersellCategory['name']) && $wisersellCategory['name'] === $categoryName) {
            $this->wisersellCategories[$wisersellCategory['name']] = $wisersellCategory['id'];
        }
    }

    public function deleteWisersellCategory($categoryName = null, $categoryId = null)
    {
        if (is_null($categoryName) && is_null($categoryId)) {
            return;
        }
        if (empty($this->wisersellCategories)) {
            $this->loadWisersellCategories();
        }
        if (!is_null($categoryName) && isset($this->wisersellCategories[$categoryName])) {
            $idToDelete = $this->wisersellCategories[$categoryName];
            $nameToDelete = $categoryName;
        }
        if (!is_null($categoryId) && in_array($categoryId, $this->wisersellCategories)) {
            $idToDelete = $categoryId;
            foreach ($this->wisersellCategories as $categoryName => $wisersellCategoryId) {
                if ($wisersellCategoryId == $categoryId) {
                    $nameToDelete = $categoryName;
                    break;
                }
            }
        }
        if (empty($idToDelete) || empty($nameToDelete)) {
            return;
        }
        $response = $this->connector->request(Connector::$apiUrl['category'], 'DELETE', "/{$idToDelete}", []);
        if (empty($response)) {
            return;
        }
        unset($this->wisersellCategories[$nameToDelete]);
    }

    public function addWisersellCategoryToPim($categoryName, $categoryId)
    {
        if (empty($this->pimCategories)) {
            $this->loadPimCategories();
        }
        if (isset($this->pimCategories[$categoryName])) {
            $this->pimCategories[$categoryName]->setWisersellCategoryId($categoryId);
            $this->pimCategories[$categoryName]->save();
            return;
        }
        $category = new Category();
        $category->setKey($categoryName);
        $category->setParent(Utility::checkSetPath('Kategoriler', Utility::checkSetPath('Ayarlar')));
        $category->setCategory($categoryName);
        $category->setWisersellCategoryId($categoryId);
        $category->save();
        $this->pimCategories[$categoryName] = $category;
    }

    public function syncCategories()
    {
        if (empty($this->pimCategories)) {
            $this->loadPimCategories();
        }
        if (empty($this->wisersellCategories)) {
            $this->loadWisersellCategories();
        }
        $wisersellCategories = $this->wisersellCategories;
        foreach ($this->pimCategories as $categoryName => $category) {
            if (isset($this->wisersellCategories[$categoryName])) {
                unset($wisersellCategories[$categoryName]);
                if ($category->isPublished()) {
                    if ($category->getWisersellCategoryId() != $this->wisersellCategories[$categoryName]) {
                        $category->setWisersellCategoryId($this->wisersellCategories[$categoryName]);
                        $category->save();
                    }                        
                } else {
                    $this->deleteWisersellCategory($categoryName);
                }
            } else {
                if ($category->isPublished()) {
                    $this->addPimCategoryToWisersell($category);
                }
            }
        }
        foreach ($wisersellCategories as $categoryName => $categoryId) {
            $this->addWisersellCategoryToPim($categoryName, $categoryId);
        }
        $this->loadPimCategories();
    }

    public function getWisersellCategoryId($categoryName)
    {
        if (empty($this->pimCategories)) {
            $this->loadPimCategories();
        }
        if (isset($this->pimCategories[$categoryName]) && $this->pimCategories[$categoryName] instanceof Category) {
            return $this->pimCategories[$categoryName]->getWisersellCategoryId();
        }
        if (isset($this->pimCategories['Diğer']) && $this->pimCategories['Diğer'] instanceof Category) {
            return $this->pimCategories['Diğer']->getWisersellCategoryId();
        }
        throw new \Exception("$categoryName için Wisersell kategorisi bulunamadı. Ayrıca Diğer kategorisi de yok.");
    }

}