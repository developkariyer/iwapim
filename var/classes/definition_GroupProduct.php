<?php

/**
 * Inheritance: no
 * Variants: no
 * Title: Product Group
 *
 * Fields Summary:
 * - products [manyToManyObjectRelation]
 * - pricingModels [manyToManyObjectRelation]
 * - frontendUrl [link]
 * - targetMarketplace [advancedManyToManyObjectRelation]
 * - iwaskuStickers [link]
 * - euStickers [link]
 * - usStickers [link]
 */

return \Pimcore\Model\DataObject\ClassDefinition::__set_state(array(
   'dao' => NULL,
   'id' => 'gproduct',
   'name' => 'GroupProduct',
   'title' => 'Product Group',
   'description' => '',
   'creationDate' => NULL,
   'modificationDate' => 1727242618,
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
      \Pimcore\Model\DataObject\ClassDefinition\Layout\Tabpanel::__set_state(array(
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
          \Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
             'name' => 'Layout',
             'type' => NULL,
             'region' => NULL,
             'title' => 'Ürünler',
             'width' => '',
             'height' => '',
             'collapsible' => false,
             'collapsed' => false,
             'bodyStyle' => '',
             'datatype' => 'layout',
             'children' => 
            array (
              0 => 
              \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation::__set_state(array(
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
                  0 => 
                  array (
                    'classes' => 'Product',
                  ),
                ),
                 'displayMode' => 'grid',
                 'pathFormatterClass' => '',
                 'maxItems' => NULL,
                 'visibleFields' => 'key,iwasku,productIdentifier,variationSize,variationColor,imageUrl,productCost',
                 'allowToCreateNewObject' => false,
                 'allowToClearRelation' => true,
                 'optimizedAdminLoading' => false,
                 'enableTextSelection' => false,
                 'visibleFieldDefinitions' => 
                array (
                ),
                 'width' => '',
                 'height' => '',
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
          1 => 
          \Pimcore\Model\DataObject\ClassDefinition\Layout\Panel::__set_state(array(
             'name' => 'PricingModels',
             'type' => NULL,
             'region' => NULL,
             'title' => 'Dağıtım Modelleri',
             'width' => '',
             'height' => '',
             'collapsible' => false,
             'collapsed' => false,
             'bodyStyle' => '',
             'datatype' => 'layout',
             'children' => 
            array (
              0 => 
              \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToManyObjectRelation::__set_state(array(
                 'name' => 'pricingModels',
                 'title' => 'Fiyatlama Modelleri',
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
                    'classes' => 'PriceModel',
                  ),
                ),
                 'displayMode' => 'grid',
                 'pathFormatterClass' => '',
                 'maxItems' => NULL,
                 'visibleFields' => 'key,pricingNodes',
                 'allowToCreateNewObject' => false,
                 'allowToClearRelation' => true,
                 'optimizedAdminLoading' => false,
                 'enableTextSelection' => false,
                 'visibleFieldDefinitions' => 
                array (
                ),
                 'width' => '',
                 'height' => '',
              )),
              1 => 
              \Pimcore\Model\DataObject\ClassDefinition\Layout\Text::__set_state(array(
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
                ),
                 'locked' => false,
                 'blockedVarsForExport' => 
                array (
                ),
                 'fieldtype' => 'text',
                 'html' => '',
                 'renderingClass' => '',
                 'renderingData' => '',
                 'border' => false,
              )),
              2 => 
              \Pimcore\Model\DataObject\ClassDefinition\Data\Link::__set_state(array(
                 'name' => 'frontendUrl',
                 'title' => 'Tablo Linki',
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
                 'allowedTypes' => 
                array (
                ),
                 'allowedTargets' => 
                array (
                  0 => '_blank',
                ),
                 'disabledFields' => 
                array (
                ),
              )),
              3 => 
              \Pimcore\Model\DataObject\ClassDefinition\Layout\Text::__set_state(array(
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
                ),
                 'locked' => false,
                 'blockedVarsForExport' => 
                array (
                ),
                 'fieldtype' => 'text',
                 'html' => 'Amazon için herhangi bir Amazon mağazasını seçip daha sonra ülke seçiniz.<div><br></div><div>Diğer pazaryerleri için ülke dikkate alınmaz.</div>',
                 'renderingClass' => '',
                 'renderingData' => '',
                 'border' => false,
              )),
              4 => 
              \Pimcore\Model\DataObject\ClassDefinition\Data\AdvancedManyToManyObjectRelation::__set_state(array(
                 'name' => 'targetMarketplace',
                 'title' => 'Hedef Pazar',
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
                 'visibleFields' => 'key',
                 'allowToCreateNewObject' => false,
                 'allowToClearRelation' => true,
                 'optimizedAdminLoading' => false,
                 'enableTextSelection' => false,
                 'visibleFieldDefinitions' => 
                array (
                ),
                 'width' => '',
                 'height' => '',
                 'allowedClassId' => 'Marketplace',
                 'columns' => 
                array (
                  0 => 
                  array (
                    'type' => 'select',
                    'position' => 1,
                    'key' => 'merchant',
                    'value' => 'US;UK;CA;AU',
                    'label' => 'Ülke',
                  ),
                ),
                 'columnKeys' => 
                array (
                  0 => 'merchant',
                ),
                 'enableBatchEdit' => false,
                 'allowMultipleAssignments' => false,
              )),
              5 => 
              \Pimcore\Model\DataObject\ClassDefinition\Data\Link::__set_state(array(
                 'name' => 'iwaskuStickers',
                 'title' => 'Iwasku Etiketleri',
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
                  0 => '_blank',
                ),
                 'disabledFields' => 
                array (
                ),
              )),
              6 => 
              \Pimcore\Model\DataObject\ClassDefinition\Data\Link::__set_state(array(
                 'name' => 'euStickers',
                 'title' => 'EU Etiketleri',
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
                  0 => '_blank',
                ),
                 'disabledFields' => 
                array (
                ),
              )),
              7 => 
              \Pimcore\Model\DataObject\ClassDefinition\Data\Link::__set_state(array(
                 'name' => 'usStickers',
                 'title' => 'ABD FNSKU Etiketleri',
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
                  0 => '_blank',
                ),
                 'disabledFields' => 
                array (
                ),
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
         'fieldtype' => 'tabpanel',
         'border' => false,
         'tabPosition' => 'top',
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
   'icon' => '/bundles/pimcoreadmin/img/flat-color-icons/star.svg',
   'group' => '',
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
