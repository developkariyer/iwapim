<?php

namespace App\Calculator;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject\ClassDefinition\Layout\DynamicTextLabelInterface;

class JsonRenderer implements DynamicTextLabelInterface
{
    public function renderLayoutText(string $data, ?Concrete $object, array $params): string
    {
        if (!$object instanceof VariantProduct) {
            return '';
        }

        $db = \Pimcore\Db::get();
        $response = $db->fetchOne('SELECT json_data FROM iwa_json_store WHERE object_id = ? AND field_name = ?', [$object->getId(), $data]);
        if (empty($response)) {
            return '';
        }
        $response = json_encode(json_decode($response), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return "<pre>$response</pre>";
    }
}