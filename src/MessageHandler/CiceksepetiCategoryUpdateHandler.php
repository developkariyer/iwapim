<?php
namespace App\MessageHandler;

use App\Message\CiceksepetiCategoryUpdateMessage;
use App\Model\DataObject\VariantProduct;
use App\Utils\Utility;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Connector\Marketplace\CiceksepetiConnector;

#[AsMessageHandler]
class CiceksepetiCategoryUpdateHandler
{
    private CiceksepetiConnector $ciceksepetiConnector;

    public function __construct(CiceksepetiConnector $ciceksepetiConnector)
    {
        $this->ciceksepetiConnector = $ciceksepetiConnector;
    }

    public function __invoke(CiceksepetiCategoryUpdateMessage $message)
    {
        $marketplaceId = $message->getMarketplaceId();

        $this->ciceksepetiConnector->setMarketplace(Marketplace::getById($marketplaceId));
        $this->ciceksepetiConnector->downloadCategories();

        $categoryIdList = $this->getCiceksepetiListingCategoriesIdList();

        foreach ($categoryIdList as $categoryId) {
            $this->ciceksepetiConnector->getCategoryAttributesAndSaveDatabase($categoryId);
        }
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
