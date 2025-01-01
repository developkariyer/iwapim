<?php

namespace App\Form\Transformer;

use App\Connector\Marketplace\Ozon\Utils;
use Doctrine\DBAL\Exception;
use InvalidArgumentException;
use Symfony\Component\Form\DataTransformerInterface;

class ProductTypeTransformer implements DataTransformerInterface
{

    /**
     * @inheritDoc
     */
    public function transform(mixed $value): mixed
    {
        if (is_array($value) && isset($value['descriptionCategoryId'], $value['typeId'])) {
            return $value['descriptionCategoryId'] . '.' . $value['typeId'];
        }
        return $value;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function reverseTransform(mixed $value): array
    {
        [$descriptionCategoryId, $typeId] = explode('.', $value);
        if (Utils::isOzonProductType($descriptionCategoryId, $typeId)) {
            return [
                'descriptionCategoryId' => $descriptionCategoryId,
                'typeId' => $typeId,
            ];
        }
        throw new InvalidArgumentException('Invalid product type');
    }
}