<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - description [textarea]
 * - pricingNodes [advancedManyToManyObjectRelation]
 * - products [advancedManyToManyObjectRelation]
 */

return \Pimcore\Model\DataObject\ClassDefinition::__set_state(array(
   'dao' => NULL,
   'id' => 'pricing',
   'name' => 'PriceModel',
   'title' => '',
   'description' => '',
   'creationDate' => NULL,
   'modificationDate' => 1725620124,
   'userOwner' => 2,
   'userModification' => 2,
   'parentClass' => '',
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
          \Pimcore\Model\DataObject\ClassDefinition\Data\Textarea::__set_state(array(
             'name' => 'description',
             'title' => 'Açıklama',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => false,
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
             'maxLength' => NULL,
             'showCharCount' => false,
             'excludeFromSearchIndex' => false,
             'height' => '',
             'width' => '',
          )),
          1 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\AdvancedManyToManyObjectRelation::__set_state(array(
             'name' => 'pricingNodes',
             'title' => 'Dağıtım Maliyetleri',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => false,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => true,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'classes' => 
            array (
              0 => 
              array (
                'classes' => 'PricingNode',
              ),
            ),
             'displayMode' => NULL,
             'pathFormatterClass' => '',
             'maxItems' => NULL,
             'visibleFields' => 'id,key,pricingValue,currency,pricingType',
             'allowToCreateNewObject' => false,
             'allowToClearRelation' => true,
             'optimizedAdminLoading' => false,
             'enableTextSelection' => true,
             'visibleFieldDefinitions' => 
            array (
            ),
             'width' => '',
             'height' => '',
             'allowedClassId' => 'PricingNode',
             'columns' => 
            array (
            ),
             'columnKeys' => 
            array (
            ),
             'enableBatchEdit' => false,
             'allowMultipleAssignments' => true,
          )),
          2 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\AdvancedManyToManyObjectRelation::__set_state(array(
             'name' => 'products',
             'title' => 'Ürünler',
             'tooltip' => '',
             'mandatory' => false,
             'noteditable' => false,
             'index' => false,
             'locked' => false,
             'style' => '',
             'permissions' => NULL,
             'fieldtype' => '',
             'relationType' => true,
             'invisible' => false,
             'visibleGridView' => false,
             'visibleSearch' => false,
             'blockedVarsForExport' => 
            array (
            ),
             'classes' => 
            array (
              0 => 
              array (
                'classes' => 'Product',
              ),
            ),
             'displayMode' => NULL,
             'pathFormatterClass' => '',
             'maxItems' => NULL,
             'visibleFields' => 'image,iwasku,key,productCost',
             'allowToCreateNewObject' => false,
             'allowToClearRelation' => true,
             'optimizedAdminLoading' => false,
             'enableTextSelection' => false,
             'visibleFieldDefinitions' => 
            array (
            ),
             'width' => '',
             'height' => '',
             'allowedClassId' => 'Product',
             'columns' => 
            array (
              0 => 
              array (
                'type' => 'number',
                'position' => 1,
                'key' => 'pricingCost',
                'label' => 'Dağıtım Maliyeti',
                'value' => '',
                'width' => NULL,
              ),
              1 => 
              array (
                'type' => 'number',
                'position' => 2,
                'key' => 'totalCost',
                'label' => 'Toplam Maliyet',
              ),
            ),
             'columnKeys' => 
            array (
              0 => 'pricingCost',
              1 => 'totalCost',
            ),
             'enableBatchEdit' => true,
             'allowMultipleAssignments' => false,
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
   'icon' => '/custom/pricemodel.svg',
   'group' => 'Maliyet',
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
      'id' => false,
      'key' => true,
      'path' => false,
      'published' => false,
      'modificationDate' => false,
      'creationDate' => false,
    ),
    'search' => 
    array (
      'id' => false,
      'key' => true,
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
