<?php

/**
 * Inheritance: yes
 * Variants: no
 * Title: Maliyet Kalemi
 *
 * Fields Summary:
 * - amount [numeric]
 * - unit [select]
 * - cost [numeric]
 * - currency [select]
 * - description [textarea]
 * - combinedCost [advancedManyToManyObjectRelation]
 * - unitCost [calculatedValue]
 */

return \Pimcore\Model\DataObject\ClassDefinition::__set_state(array(
   'dao' => NULL,
   'id' => 'cost',
   'name' => 'CostNode',
   'title' => 'Maliyet Kalemi',
   'description' => '',
   'creationDate' => NULL,
   'modificationDate' => 1725614885,
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
   'allowInherit' => true,
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
                 'name' => 'amount',
                 'title' => 'Miktar',
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
                 'visibleSearch' => true,
                 'blockedVarsForExport' => 
                array (
                ),
                 'defaultValue' => 1,
                 'integer' => false,
                 'unsigned' => true,
                 'minValue' => 0.0,
                 'maxValue' => NULL,
                 'unique' => false,
                 'decimalSize' => 12,
                 'decimalPrecision' => 2,
                 'width' => 100,
                 'defaultValueGenerator' => '',
              )),
              1 => 
              \Pimcore\Model\DataObject\ClassDefinition\Data\Select::__set_state(array(
                 'name' => 'unit',
                 'title' => 'Birim',
                 'tooltip' => '',
                 'mandatory' => true,
                 'noteditable' => false,
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
                 'options' => 
                array (
                  0 => 
                  array (
                    'key' => 'metre',
                    'value' => 'metre',
                  ),
                  1 => 
                  array (
                    'key' => 'metrekare',
                    'value' => 'metrekare',
                  ),
                  2 => 
                  array (
                    'key' => 'metreküp',
                    'value' => 'metreküp',
                  ),
                  3 => 
                  array (
                    'key' => 'adet',
                    'value' => 'adet',
                  ),
                  4 => 
                  array (
                    'key' => 'takım/set',
                    'value' => 'takım',
                  ),
                  5 => 
                  array (
                    'key' => 'kilogram',
                    'value' => 'kilogram',
                  ),
                  6 => 
                  array (
                    'key' => 'litre',
                    'value' => 'litre',
                  ),
                  7 => 
                  array (
                    'key' => 'saat',
                    'value' => 'saat',
                  ),
                ),
                 'defaultValue' => 'adet',
                 'columnLength' => 190,
                 'dynamicOptions' => false,
                 'defaultValueGenerator' => '',
                 'width' => 150,
                 'optionsProviderType' => 'configure',
                 'optionsProviderClass' => '',
                 'optionsProviderData' => '',
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
          1 => 
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
                 'name' => 'cost',
                 'title' => 'Tutar',
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
                 'decimalSize' => 12,
                 'decimalPrecision' => 3,
                 'width' => 100,
                 'defaultValueGenerator' => '',
              )),
              1 => 
              \Pimcore\Model\DataObject\ClassDefinition\Data\Select::__set_state(array(
                 'name' => 'currency',
                 'title' => 'Para Birimi',
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
                 'visibleGridView' => true,
                 'visibleSearch' => true,
                 'blockedVarsForExport' => 
                array (
                ),
                 'defaultValue' => NULL,
                 'columnLength' => 190,
                 'dynamicOptions' => true,
                 'defaultValueGenerator' => '',
                 'width' => 150,
                 'optionsProviderType' => 'class',
                 'optionsProviderClass' => 'App\\Select\\CurrencyCodes',
                 'optionsProviderData' => '',
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
          2 => 
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
             'height' => 50,
             'width' => 300,
          )),
          3 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\AdvancedManyToManyObjectRelation::__set_state(array(
             'name' => 'combinedCost',
             'title' => 'Birleşik Hammaddeler',
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
             'visibleFields' => 'key,unit,unitCost,modificationDate',
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
                'key' => 'amount',
                'label' => 'Sarf',
              ),
            ),
             'columnKeys' => 
            array (
              0 => 'amount',
            ),
             'enableBatchEdit' => false,
             'allowMultipleAssignments' => false,
          )),
          4 => 
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
             'html' => 'Birim maliyet, sistemde yüklü döviz kurları kullanılarak canlı hesaplanır.',
             'renderingClass' => '',
             'renderingData' => '',
             'border' => false,
          )),
          5 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\CalculatedValue::__set_state(array(
             'name' => 'unitCost',
             'title' => 'Birim Maliyet (TL)',
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
             'elementType' => 'numeric',
             'calculatorType' => 'class',
             'calculatorExpression' => 'object.getCost() / object.getAmount() ~ \'\'',
             'calculatorClass' => 'App\\Calculator\\UnitCostCalculator',
             'columnLength' => 190,
             'width' => 200,
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
   'icon' => '/custom/costnode.svg',
   'group' => 'Maliyet',
   'showAppLoggerTab' => false,
   'linkGeneratorReference' => '',
   'previewGeneratorReference' => '',
   'compositeIndices' => 
  array (
  ),
   'showFieldLookup' => true,
   'propertyVisibility' => 
  array (
    'grid' => 
    array (
      'id' => true,
      'key' => true,
      'path' => false,
      'published' => false,
      'modificationDate' => true,
      'creationDate' => false,
    ),
    'search' => 
    array (
      'id' => true,
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
