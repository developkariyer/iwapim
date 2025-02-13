<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - description [textarea]
 * - costNodes [advancedManyToManyObjectRelation]
 * - products [reverseObjectRelation]
 * - variants [reverseObjectRelation]
 */

return \Pimcore\Model\DataObject\ClassDefinition::__set_state(array(
   'dao' => NULL,
   'id' => 'modelcost',
   'name' => 'CostModel',
   'title' => '',
   'description' => '',
   'creationDate' => NULL,
   'modificationDate' => 1726323040,
   'userOwner' => 2,
   'userModification' => 2,
   'parentClass' => 'App\\Model\\DataObject\\CostModel',
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
             'visibleGridView' => false,
             'visibleSearch' => false,
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
             'name' => 'costNodes',
             'title' => 'Cost Nodes',
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
                'classes' => 'CostNode',
              ),
            ),
             'displayMode' => NULL,
             'pathFormatterClass' => '',
             'maxItems' => NULL,
             'visibleFields' => 'key,unitCost,unit',
             'allowToCreateNewObject' => false,
             'allowToClearRelation' => true,
             'optimizedAdminLoading' => false,
             'enableTextSelection' => true,
             'visibleFieldDefinitions' => 
            array (
            ),
             'width' => '',
             'height' => '',
             'allowedClassId' => 'CostNode',
             'columns' => 
            array (
              0 => 
              array (
                'type' => 'number',
                'position' => 1,
                'key' => 'sarf',
                'label' => 'Sarf',
                'value' => '',
              ),
              1 => 
              array (
                'type' => 'text',
                'position' => 2,
                'key' => 'description',
                'label' => 'Açıklama',
              ),
              2 => 
              array (
                'type' => 'select',
                'position' => 3,
                'key' => 'factor',
                'label' => 'Çarpan',
                'value' => 'Beher Ürün;Ebat m2;Ambalaj m2;Kesim Detay',
              ),
            ),
             'columnKeys' => 
            array (
              0 => 'sarf',
              1 => 'description',
              2 => 'factor',
            ),
             'enableBatchEdit' => true,
             'allowMultipleAssignments' => true,
          )),
          2 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\ReverseObjectRelation::__set_state(array(
             'name' => 'products',
             'title' => 'Products',
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
            ),
             'displayMode' => NULL,
             'pathFormatterClass' => '',
             'maxItems' => NULL,
             'visibleFields' => 'key,productCategory,variationSizeList,variationColorList,imageUrl,productCost',
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
             'ownerFieldName' => 'costModelProduct',
             'lazyLoading' => true,
          )),
          3 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\ReverseObjectRelation::__set_state(array(
             'name' => 'variants',
             'title' => 'Variants',
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
            ),
             'displayMode' => NULL,
             'pathFormatterClass' => '',
             'maxItems' => NULL,
             'visibleFields' => 'key,variationSize,variationColor,memberOfBundle,imageUrl,productCost',
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
             'ownerFieldName' => 'costModelVariant',
             'lazyLoading' => true,
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
   'icon' => '/custom/costmodel.svg',
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
      'id' => true,
      'key' => false,
      'path' => true,
      'published' => true,
      'modificationDate' => true,
      'creationDate' => true,
    ),
    'search' => 
    array (
      'id' => true,
      'key' => false,
      'path' => true,
      'published' => true,
      'modificationDate' => true,
      'creationDate' => true,
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
