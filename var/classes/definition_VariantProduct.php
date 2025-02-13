<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - title [input]
 * - imageGallery [imageGallery]
 * - imageUrl [externalImage]
 * - urlLink [link]
 * - stock [table]
 * - lastUpdate [datetime]
 * - salePrice [numeric]
 * - saleCurrency [input]
 * - attributes [input]
 * - last7Orders [calculatedValue]
 * - last30Orders [calculatedValue]
 * - totalOrders [calculatedValue]
 * - uniqueMarketplaceId [input]
 * - marketplace [manyToOneRelation]
 * - mainProduct [reverseObjectRelation]
 * - amazonMarketplace [fieldcollections]
 * - sellerSku [input]
 * - quantity [numeric]
 * - calculatedWisersellCode [input]
 * - wisersellVariantCode [input]
 * - wisersellVariantJson [textarea]
 * - countMainProduct [numeric]
 * - fnsku [input]
 * - marketplaceType [input]
 * - ean [input]
 */

return \Pimcore\Model\DataObject\ClassDefinition::__set_state(array(
   'dao' => NULL,
   'id' => 'varyantproduct',
   'name' => 'VariantProduct',
   'title' => '',
   'description' => '',
   'creationDate' => NULL,
   'modificationDate' => 1738965486,
   'userOwner' => 2,
   'userModification' => 2,
   'parentClass' => 'App\\Model\\DataObject\\VariantProduct',
   'implementsInterfaces' => '',
   'listingParentClass' => '',
   'useTraits' => '',
   'listingUseTraits' => '',
   'encryption' => false,
   'encryptedTables' => 
  array (
  ),
   'allowInherit' => false,
   'allowVariants' => false,
   'showVariants' => false,
   'layoutDefinitions' => 
  \Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
     'name' => 'pimcore_root',
     'type' => NULL,
     'region' => NULL,
     'title' => NULL,
     'width' => 0,
     'height' => 0,
     'collapsible' => false,
     'collapsed' => false,
     'bodyStyle' => NULL,
     'datatype' => 'layout',
     'children' => 
    array (
      0 => 
      \Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
         'name' => 'Layout',
         'type' => NULL,
         'region' => NULL,
         'title' => '',
         'width' => '',
         'height' => '',
         'collapsible' => false,
         'collapsed' => false,
         'bodyStyle' => '',
         'datatype' => 'layout',
         'children' => 
        array (
          0 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
             'name' => 'title',
             'title' => 'Title',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => true,
             'visibleSearch' => true,
             'blockedVarsForExport' => 
            array (
            ),
             'defaultValue' => NULL,
             'columnLength' => 1023,
             'regex' => '',
             'regexFlags' => 
            array (
            ),
             'unique' => false,
             'showCharCount' => false,
             'width' => 500,
             'defaultValueGenerator' => '',
          )),
          1 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\ImageGallery::__set_state(array(
             'name' => 'imageGallery',
             'title' => 'Image Gallery',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'uploadPath' => '',
             'ratioX' => NULL,
             'ratioY' => NULL,
             'predefinedDataTemplates' => '',
             'height' => '',
             'width' => '',
          )),
          2 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\ExternalImage::__set_state(array(
             'name' => 'imageUrl',
             'title' => 'Image Url',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => true,
             'visibleSearch' => true,
             'blockedVarsForExport' => 
            array (
            ),
             'previewWidth' => NULL,
             'inputWidth' => NULL,
             'previewHeight' => NULL,
          )),
          3 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Link::__set_state(array(
             'name' => 'urlLink',
             'title' => 'Url Link',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'allowedTypes' => 
            array (
            ),
             'allowedTargets' => 
            array (
            ),
             'disabledFields' => 
            array (
            ),
          )),
          4 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Table::__set_state(array(
             'name' => 'stock',
             'title' => 'Stok',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'cols' => NULL,
             'colsFixed' => false,
             'rows' => NULL,
             'rowsFixed' => false,
             'data' => '',
             'columnConfigActivated' => false,
             'columnConfig' => 
            array (
            ),
             'height' => '',
             'width' => 320,
          )),
          5 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Datetime::__set_state(array(
             'name' => 'lastUpdate',
             'title' => 'Last Update',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => true,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'defaultValue' => NULL,
             'useCurrentDate' => true,
             'respectTimezone' => false,
             'columnType' => 'datetime',
             'defaultValueGenerator' => '',
          )),
          6 => 
          \Pimcore\Model\DataObject\ClassDefinition\Layout\Fieldcontainer::__set_state(array(
             'name' => 'Field Container',
             'type' => NULL,
             'region' => NULL,
             'title' => NULL,
             'width' => '',
             'height' => '',
             'collapsible' => false,
             'collapsed' => false,
             'bodyStyle' => '',
             'datatype' => 'layout',
             'children' => 
            array (
              0 => 
              \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric::__set_state(array(
                 'name' => 'salePrice',
                 'title' => 'Sale Price',
                 'tooltip' => '',
                 'mandatory' => false,
                 'noteditable' => true,
                 'index' => false,
                 'locked' => false,
                 'style' => '',
                 'permissions' => NULL,
                 'fieldtype' => '',
                 'relationType' => false,
                 'invisible' => false,
                 'visibleGridView' => true,
                 'visibleSearch' => true,
                 'blockedVarsForExport' => 
                array (
                ),
                 'defaultValue' => NULL,
                 'integer' => false,
                 'unsigned' => false,
                 'minValue' => NULL,
                 'maxValue' => NULL,
                 'unique' => false,
                 'decimalSize' => 10,
                 'decimalPrecision' => 2,
                 'width' => '',
                 'defaultValueGenerator' => '',
              )),
              1 => 
              \Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
                 'name' => 'saleCurrency',
                 'title' => 'Sale Currency',
                 'tooltip' => '',
                 'mandatory' => false,
                 'noteditable' => true,
                 'index' => false,
                 'locked' => false,
                 'style' => '',
                 'permissions' => NULL,
                 'fieldtype' => '',
                 'relationType' => false,
                 'invisible' => false,
                 'visibleGridView' => false,
                 'visibleSearch' => false,
                 'blockedVarsForExport' => 
                array (
                ),
                 'defaultValue' => NULL,
                 'columnLength' => 190,
                 'regex' => '',
                 'regexFlags' => 
                array (
                ),
                 'unique' => false,
                 'showCharCount' => false,
                 'width' => '',
                 'defaultValueGenerator' => '',
              )),
            ),
             'locked' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'fieldtype' => 'fieldcontainer',
             'layout' => 'hbox',
             'fieldLabel' => '',
             'labelWidth' => 100,
             'labelAlign' => 'left',
          )),
          7 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
             'name' => 'attributes',
             'title' => 'Attributes',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => true,
             'visibleSearch' => true,
             'blockedVarsForExport' => 
            array (
            ),
             'defaultValue' => NULL,
             'columnLength' => 300,
             'regex' => '',
             'regexFlags' => 
            array (
            ),
             'unique' => false,
             'showCharCount' => false,
             'width' => '',
             'defaultValueGenerator' => '',
          )),
          8 => 
          \Pimcore\Model\DataObject\ClassDefinition\Layout\Fieldcontainer::__set_state(array(
             'name' => 'Field Container',
             'type' => NULL,
             'region' => NULL,
             'title' => NULL,
             'width' => '',
             'height' => '',
             'collapsible' => false,
             'collapsed' => false,
             'bodyStyle' => '',
             'datatype' => 'layout',
             'children' => 
            array (
              0 => 
              \Pimcore\Model\DataObject\ClassDefinition\Data\CalculatedValue::__set_state(array(
                 'name' => 'last7Orders',
                 'title' => 'Son 7 Gün Satış',
                 'tooltip' => '',
                 'mandatory' => false,
                 'noteditable' => true,
                 'index' => false,
                 'locked' => false,
                 'style' => '',
                 'permissions' => NULL,
                 'fieldtype' => '',
                 'relationType' => false,
                 'invisible' => false,
                 'visibleGridView' => false,
                 'visibleSearch' => false,
                 'blockedVarsForExport' => 
                array (
                ),
                 'elementType' => 'numeric',
                 'calculatorType' => 'class',
                 'calculatorExpression' => '',
                 'calculatorClass' => 'App\\Calculator\\OrdersCalculator',
                 'columnLength' => 190,
                 'width' => '',
              )),
              1 => 
              \Pimcore\Model\DataObject\ClassDefinition\Data\CalculatedValue::__set_state(array(
                 'name' => 'last30Orders',
                 'title' => 'Son 30 Gün Satış',
                 'tooltip' => '',
                 'mandatory' => false,
                 'noteditable' => true,
                 'index' => false,
                 'locked' => false,
                 'style' => '',
                 'permissions' => NULL,
                 'fieldtype' => '',
                 'relationType' => false,
                 'invisible' => false,
                 'visibleGridView' => false,
                 'visibleSearch' => false,
                 'blockedVarsForExport' => 
                array (
                ),
                 'elementType' => 'numeric',
                 'calculatorType' => 'class',
                 'calculatorExpression' => '',
                 'calculatorClass' => 'App\\Calculator\\OrdersCalculator',
                 'columnLength' => 190,
                 'width' => '',
              )),
              2 => 
              \Pimcore\Model\DataObject\ClassDefinition\Data\CalculatedValue::__set_state(array(
                 'name' => 'totalOrders',
                 'title' => 'Toplam Satış',
                 'tooltip' => '',
                 'mandatory' => false,
                 'noteditable' => true,
                 'index' => false,
                 'locked' => false,
                 'style' => '',
                 'permissions' => NULL,
                 'fieldtype' => '',
                 'relationType' => false,
                 'invisible' => false,
                 'visibleGridView' => false,
                 'visibleSearch' => false,
                 'blockedVarsForExport' => 
                array (
                ),
                 'elementType' => 'numeric',
                 'calculatorType' => 'class',
                 'calculatorExpression' => '',
                 'calculatorClass' => 'App\\Calculator\\OrdersCalculator',
                 'columnLength' => 190,
                 'width' => '',
              )),
            ),
             'locked' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'fieldtype' => 'fieldcontainer',
             'layout' => 'hbox',
             'fieldLabel' => '',
             'labelWidth' => 100,
             'labelAlign' => 'left',
          )),
          9 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
             'name' => 'uniqueMarketplaceId',
             'title' => 'Unique Marketplace Id',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => true,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => true,
             'visibleSearch' => true,
             'blockedVarsForExport' => 
            array (
            ),
             'defaultValue' => NULL,
             'columnLength' => 450,
             'regex' => '',
             'regexFlags' => 
            array (
            ),
             'unique' => false,
             'showCharCount' => false,
             'width' => '',
             'defaultValueGenerator' => '',
          )),
          10 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation::__set_state(array(
             'name' => 'marketplace',
             'title' => 'Marketplace',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => true,
             'invisible' => false,
             'visibleGridView' => true,
             'visibleSearch' => true,
             'blockedVarsForExport' => 
            array (
            ),
             'classes' => 
            array (
              0 => 
              array (
                'classes' => 'Marketplace',
              ),
            ),
             'displayMode' => 'grid',
             'pathFormatterClass' => '',
             'assetInlineDownloadAllowed' => false,
             'assetUploadPath' => '',
             'allowToClearRelation' => true,
             'objectsAllowed' => true,
             'assetsAllowed' => false,
             'assetTypes' => 
            array (
            ),
             'documentsAllowed' => false,
             'documentTypes' => 
            array (
            ),
             'width' => '',
          )),
          11 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\ReverseObjectRelation::__set_state(array(
             'name' => 'mainProduct',
             'title' => 'Main Product',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => true,
             'invisible' => false,
             'visibleGridView' => true,
             'visibleSearch' => true,
             'blockedVarsForExport' => 
            array (
            ),
             'classes' => 
            array (
            ),
             'displayMode' => NULL,
             'pathFormatterClass' => '',
             'maxItems' => NULL,
             'visibleFields' => 'id,key,variationSize,variationColor,image',
             'allowToCreateNewObject' => false,
             'allowToClearRelation' => true,
             'optimizedAdminLoading' => false,
             'enableTextSelection' => false,
             'visibleFieldDefinitions' => 
            array (
            ),
             'width' => '',
             'height' => '',
             'ownerClassName' => 'Product',
             'ownerClassId' => 'product',
             'ownerFieldName' => 'listingItems',
             'lazyLoading' => true,
          )),
          12 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections::__set_state(array(
             'name' => 'amazonMarketplace',
             'title' => 'Amazon Marketplace',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'allowedTypes' => 
            array (
              0 => 'AmazonMarketplace',
            ),
             'lazyLoading' => true,
             'maxItems' => NULL,
             'disallowAddRemove' => true,
             'disallowReorder' => true,
             'collapsed' => false,
             'collapsible' => false,
             'border' => true,
          )),
          13 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
             'name' => 'sellerSku',
             'title' => 'Girilen SKU',
             'tooltip' => 'Bu numara güvenilir, tekil bir bilgi değildir. Listing yaparken girilen veriyi gösterir.',
             'mandatory' => false,
             'noteditable' => true,
             'index' => true,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'defaultValue' => NULL,
             'columnLength' => 190,
             'regex' => '',
             'regexFlags' => 
            array (
            ),
             'unique' => false,
             'showCharCount' => false,
             'width' => '',
             'defaultValueGenerator' => '',
          )),
          14 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric::__set_state(array(
             'name' => 'quantity',
             'title' => 'Miktar',
             'tooltip' => 'Listing yaparken girilen stok miktarını gösterir. Gerçek durumu yansıtmayabilir.',
             'mandatory' => false,
             'noteditable' => true,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'defaultValue' => NULL,
             'integer' => false,
             'unsigned' => false,
             'minValue' => NULL,
             'maxValue' => NULL,
             'unique' => false,
             'decimalSize' => NULL,
             'decimalPrecision' => NULL,
             'width' => '',
             'defaultValueGenerator' => '',
          )),
          15 => 
          \Pimcore\Model\DataObject\ClassDefinition\Layout\Text::__set_state(array(
             'name' => 'apiResponseJson',
             'type' => NULL,
             'region' => NULL,
             'title' => 'API Json',
             'width' => '',
             'height' => '',
             'collapsible' => true,
             'collapsed' => true,
             'bodyStyle' => '',
             'datatype' => 'layout',
             'children' => 
            array (
            ),
             'locked' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'fieldtype' => 'text',
             'html' => '',
             'renderingClass' => 'App\\Calculator\\JsonRenderer',
             'renderingData' => 'apiResponseJson',
             'border' => true,
          )),
          16 => 
          \Pimcore\Model\DataObject\ClassDefinition\Layout\Text::__set_state(array(
             'name' => 'parentResponseJson',
             'type' => NULL,
             'region' => NULL,
             'title' => 'Parent Json',
             'width' => '',
             'height' => '',
             'collapsible' => true,
             'collapsed' => true,
             'bodyStyle' => '',
             'datatype' => 'layout',
             'children' => 
            array (
            ),
             'locked' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'fieldtype' => 'text',
             'html' => '',
             'renderingClass' => 'App\\Calculator\\JsonRenderer',
             'renderingData' => 'parentResponseJson',
             'border' => true,
          )),
          17 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
             'name' => 'calculatedWisersellCode',
             'title' => 'Calculated Wisersell Code',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => true,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'defaultValue' => NULL,
             'columnLength' => 190,
             'regex' => '',
             'regexFlags' => 
            array (
            ),
             'unique' => false,
             'showCharCount' => false,
             'width' => '',
             'defaultValueGenerator' => '',
          )),
          18 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
             'name' => 'wisersellVariantCode',
             'title' => 'Wisersell Variant Code',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => true,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'defaultValue' => NULL,
             'columnLength' => 190,
             'regex' => '',
             'regexFlags' => 
            array (
            ),
             'unique' => false,
             'showCharCount' => false,
             'width' => '',
             'defaultValueGenerator' => '',
          )),
          19 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Textarea::__set_state(array(
             'name' => 'wisersellVariantJson',
             'title' => 'Wisersell Variant Json',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'maxLength' => NULL,
             'showCharCount' => false,
             'excludeFromSearchIndex' => true,
             'height' => '',
             'width' => '',
          )),
          20 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Numeric::__set_state(array(
             'name' => 'countMainProduct',
             'title' => 'Count Main Product',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => false,
             'index' => true,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'defaultValue' => NULL,
             'integer' => false,
             'unsigned' => false,
             'minValue' => NULL,
             'maxValue' => NULL,
             'unique' => false,
             'decimalSize' => NULL,
             'decimalPrecision' => NULL,
             'width' => '',
             'defaultValueGenerator' => '',
          )),
          21 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
             'name' => 'fnsku',
             'title' => 'Fnsku',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'defaultValue' => NULL,
             'columnLength' => 190,
             'regex' => '',
             'regexFlags' => 
            array (
            ),
             'unique' => false,
             'showCharCount' => false,
             'width' => '',
             'defaultValueGenerator' => '',
          )),
          22 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
             'name' => 'marketplaceType',
             'title' => 'Pazaryeri Tipi',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => true,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => true,
             'visibleSearch' => true,
             'blockedVarsForExport' => 
            array (
            ),
             'defaultValue' => NULL,
             'columnLength' => 190,
             'regex' => '',
             'regexFlags' => 
            array (
            ),
             'unique' => false,
             'showCharCount' => false,
             'width' => '',
             'defaultValueGenerator' => '',
          )),
          23 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Input::__set_state(array(
             'name' => 'ean',
             'title' => 'Ean',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => true,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => false,
             'invisible' => false,
             'visibleGridView' => true,
             'visibleSearch' => true,
             'blockedVarsForExport' => 
            array (
            ),
             'defaultValue' => NULL,
             'columnLength' => 190,
             'regex' => '',
             'regexFlags' => 
            array (
            ),
             'unique' => false,
             'showCharCount' => false,
             'width' => '',
             'defaultValueGenerator' => '',
          )),
        ),
         'locked' => false,
         'blockedVarsForExport' => 
        array (
        ),
         'fieldtype' => 'panel',
         'layout' => NULL,
         'border' => false,
         'icon' => '',
         'labelWidth' => 100,
         'labelAlign' => 'left',
      )),
    ),
     'locked' => false,
     'blockedVarsForExport' => 
    array (
    ),
     'fieldtype' => 'panel',
     'layout' => NULL,
     'border' => false,
     'icon' => NULL,
     'labelWidth' => 100,
     'labelAlign' => 'left',
  )),
   'icon' => '/bundles/pimcoreadmin/img/flat-color-icons/list.svg',
   'group' => 'Pazaryeri',
   'showAppLoggerTab' => false,
   'linkGeneratorReference' => '',
   'previewGeneratorReference' => '',
   'compositeIndices' => 
  array (
  ),
   'showFieldLookup' => false,
   'propertyVisibility' => 
  array (
    'grid' => 
    array (
      'id' => true,
      'key' => false,
      'path' => false,
      'published' => false,
      'modificationDate' => false,
      'creationDate' => false,
    ),
    'search' => 
    array (
      'id' => true,
      'key' => false,
      'path' => false,
      'published' => false,
      'modificationDate' => false,
      'creationDate' => false,
    ),
  ),
   'enableGridLocking' => false,
   'deletedDataComponents' => 
  array (
  ),
   'blockedVarsForExport' => 
  array (
  ),
   'fieldDefinitionsCache' => 
  array (
  ),
   'activeDispatchingEvents' => 
  array (
  ),
));
