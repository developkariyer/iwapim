<?php

namespace App\Connector\Wisersell;

use Exception;
use Pimcore\Model\DataObject\Category;
use App\Utils\Utility;
use Pimcore\Model\Element\DuplicateFullPathException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CategorySyncService
{
    protected Connector $connector;
    public array $pimCategories = []; // [categoryName => Category]
    public array $wisersellCategories = []; // [categoryName => categoryId]

    public function __construct(Connector $connector)
    {   
        $this->connector = $connector;
    }

    public function loadPimCategories($force = false): void
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

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function loadWisersellCategories($force = false): int
    {
        if (!$force && !empty($this->wisersellCategories)) {
            return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/categories.json');
        }
        $this->wisersellCategories = json_decode(Utility::getCustomCache('categories.json', PIMCORE_PROJECT_ROOT . '/tmp/wisersell'), true) ?? [];
        if (!$force && !empty($this->wisersellCategories)) {
            return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/categories.json');
        }
        $response = $this->connector->request(Connector::$apiUrl['category'], 'GET');
        if (empty($response)) {
            return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/categories.json');
        }
        $result = $response->toArray();
        $this->wisersellCategories = [];
        foreach ($result as $wisersellCategory) {
            if (isset($wisersellCategory['id']) && isset($wisersellCategory['name'])) {
                $this->wisersellCategories[$wisersellCategory['name']] = $wisersellCategory['id'];
            }
        }
        Utility::setCustomCache('categories.json', PIMCORE_PROJECT_ROOT . '/tmp/wisersell', json_encode($this->wisersellCategories));
        return time()-filemtime(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/categories.json');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function load($force = false): int
    {
        $this->loadPimCategories($force);
        return $this->loadWisersellCategories($force);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function status(): array
    {
        $cacheExpire = $this->load();
        return [
            'pim' => count($this->pimCategories),
            'wisersell' => count($this->wisersellCategories),
            'expire' => 86400-$cacheExpire
        ];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function dump(): void
    {
        $this->load();
        file_put_contents(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/categories.wisersell.txt', print_r($this->wisersellCategories, true));
        file_put_contents(PIMCORE_PROJECT_ROOT . '/tmp/wisersell/categories.pim.txt', print_r($this->pimCategories, true));
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Exception
     */
    public function addPimCategoryToWisersell($category): void
    {
        if (!($category instanceof Category)) {
            return;
        }
        $this->load();
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

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function updateWisersellCategory($categoryId, $categoryName): void
    {
        $this->load();
        if (isset($this->wisersellCategories[$categoryName]) && $this->wisersellCategories[$categoryName] == $categoryId) {
            return;
        }
        $response = $this->connector->request(Connector::$apiUrl['category'], 'PUT', "/{$categoryId}", ['name' => $categoryName]);
        if (empty($response)) {
            return;
        }
        $wisersellCategory = $response->toArray();
        if (isset($wisersellCategory['id']) && isset($wisersellCategory['name'])) {
            $this->wisersellCategories[$wisersellCategory['name']] = $wisersellCategory['id'];
        }
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function deleteWisersellCategory($categoryName = null, $categoryId = null): void
    {
        if (is_null($categoryName) ^ is_null($categoryId)) { // XOR
            return;
        }
        $this->load();
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
        $response = $this->connector->request(Connector::$apiUrl['category'], 'DELETE', "/{$idToDelete}");
        if (empty($response)) {
            return;
        }
        if ($response->getStatusCode() != 200) {
            echo $response->getContent();
        }
        unset($this->wisersellCategories[$nameToDelete]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws DuplicateFullPathException
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function addWisersellCategoryToPim($categoryName, $categoryId): void
    {
        $this->load();
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

    /**
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DuplicateFullPathException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function sync(): void
    {
        $this->load();
        echo "Categories loaded Pim(".count($this->pimCategories).") Wisersell(".count($this->wisersellCategories).") categories.\n";
        $wisersellCategories = $this->wisersellCategories;
        foreach ($this->pimCategories as $categoryName => $category) {
            echo "  Syncing PIM category $categoryName";
            if (isset($this->wisersellCategories[$categoryName])) {
                echo " Wisersell";
                unset($wisersellCategories[$categoryName]);
                if ($category->isPublished()) {
                    echo " Published";
                    if ($category->getWisersellCategoryId() != $this->wisersellCategories[$categoryName]) {
                        echo " ID_Updated";
                        $category->setWisersellCategoryId($this->wisersellCategories[$categoryName]);
                        $category->save();
                    }                        
                } else {
                    echo " Unpublished";
                    $this->deleteWisersellCategory($categoryName);
                    echo " Deleted";
                }
            } else {
                if ($category->isPublished()) {
                    echo " Published";
                    $this->addPimCategoryToWisersell($category);
                    echo " Added";
                }
            }
            echo "\n";
        }
        foreach ($wisersellCategories as $categoryName => $categoryId) {
            echo "  Syncing Wisersell category $categoryName to PIM\n";
            $this->addWisersellCategoryToPim($categoryName, $categoryId);
        }
        $this->loadPimCategories(true);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function getWisersellCategoryId($categoryName): ?string
    {
        $this->load();
        if (isset($this->pimCategories[$categoryName]) && $this->pimCategories[$categoryName] instanceof Category) {
            return $this->pimCategories[$categoryName]->getWisersellCategoryId();
        }
        if (isset($this->pimCategories['Diğer']) && $this->pimCategories['Diğer'] instanceof Category) {
            return $this->pimCategories['Diğer']->getWisersellCategoryId();
        }
        throw new Exception("$categoryName için Wisersell kategorisi bulunamadı. Ayrıca Diğer kategorisi de yok.");
    }

}