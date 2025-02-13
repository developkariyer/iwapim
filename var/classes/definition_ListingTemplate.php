<?php

/**
 * Inheritance: no
 * Variants: no
 *
 * Fields Summary:
 * - marketplace [manyToOneRelation]
 */

return \Pimcore\Model\DataObject\ClassDefinition::__set_state(array(
   'dao' => NULL,
   'id' => 'listingTemplate',
   'name' => 'ListingTemplate',
   'title' => '',
   'description' => '',
   'creationDate' => NULL,
   'modificationDate' => 1735996259,
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
          \Pimcore\Model\DataObject\ClassDefinition\Data\ManyToOneRelation::__set_state(array(
             'name' => 'marketplace',
             'title' => 'Market',
             'tooltip' => '',
             'mandatory' => true,
             'noteditable' => false,
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
   'icon' => '',
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
      'key' => true,
      'path' => false,
      'published' => true,
      'modificationDate' => false,
      'creationDate' => false,
    ),
    'search' => 
    array (
      'id' => true,
      'key' => true,
      'path' => false,
      'published' => true,
      'modificationDate' => false,
      'creationDate' => false,
    ),
  ),
   'enableGridLocking' => false,
   'deletedDataComponents' => 
  array (
    0 => 
    \Pimcore\Model\DataObject\ClassDefinition\Data\AdvancedManyToManyObjectRelation::__set_state(array(
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
       'visibleFields' => 'imageUrl,iwasku,key,published,variationSize,variationColor',
       'allowToCreateNewObject' => false,
       'allowToClearRelation' => true,
       'optimizedAdminLoading' => true,
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
          'key' => 'listing',
          'label' => 'Listing',
          'value' => '',
        ),
        1 => 
        array (
          'type' => 'number',
          'position' => 2,
          'key' => 'grouptype',
          'label' => 'Grup',
        ),
        2 => 
        array (
          'type' => 'number',
          'position' => 3,
          'key' => 'producttype',
          'label' => 'Ürün Tipi',
        ),
      ),
       'columnKeys' => 
      array (
        0 => 'listing',
        1 => 'grouptype',
        2 => 'producttype',
      ),
       'enableBatchEdit' => false,
       'allowMultipleAssignments' => false,
    )),
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
