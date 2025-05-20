<?php
namespace App\MessageHandler;

use App\Connector\Marketplace\CiceksepetiConnector;
use App\Message\CiceksepetiCategoryUpdateMessage;
use App\Model\DataObject\Marketplace;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CiceksepetiCategoryUpdateHandler
{
    public function __construct()
    {
    }

    public function __invoke(CiceksepetiCategoryUpdateMessage $message)
    {
        $marketplaceId = $message->getMarketplaceId();
        $this->ciceksepetiConnector = new CiceksepetiConnector(Marketplace::getById($marketplaceId));
        echo "Ciceksepeti Connector Created\n";
        $this->ciceksepetiConnector->downloadCategories();
        echo "Ciceksepeti Downloaded Categories\n";
        $categoryIdList = $this->getCiceksepetiListingCategoriesIdList();
        echo "Ciceksepeti Category List Updated\n";
        foreach ($categoryIdList as $categoryId) {
            $this->ciceksepetiConnector->getCategoryAttributesAndSaveDatabase($categoryId);
        }
        echo "Ciceksepeti Category Attributes Updated\n";
    }

    public function getCiceksepetiListingCategoriesIdList(): array
    {
        $sql = "SELECT oo_id FROM `object_query_varyantproduct` WHERE marketplaceType = 'Ciceksepeti'";
        $ciceksepetiVariantIds = Utility::fetchFromSql($sql);
        if (!is_array($ciceksepetiVariantIds) || empty($ciceksepetiVariantIds)) {
            return [];
        }
        $categoryIdList = [];
        foreach ($ciceksepetiVariantIds as $ciceksepetiVariantId) {
            $variantProduct = VariantProduct::getById($ciceksepetiVariantId['oo_id']);
            if (!$variantProduct instanceof VariantProduct) {
                continue;
            }
            $apiData = json_decode($variantProduct->jsonRead('apiResponseJson'), true);
            $categoryIdList[] = $apiData['categoryId'];
        }
        return array_unique($categoryIdList);
    }
}
