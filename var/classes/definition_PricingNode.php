<?php

/**
 * Inheritance: yes
 * Variants: no
 * Title: Fiyatlama Kalemi
 *
 * Fields Summary:
 * - pricingValue [numeric]
 * - currency [select]
 * - pricingType [select]
 * - nodeDescription [textarea]
 */

return \Pimcore\Model\DataObject\ClassDefinition::__set_state(array(
   'dao' => NULL,
   'id' => 'price',
   'name' => 'PricingNode',
   'title' => 'Fiyatlama Kalemi',
   'description' => '',
   'creationDate' => NULL,
   'modificationDate' => 1725617018,
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
             'html' => '<div style=""><font face="Open Sans, Helvetica Neue, helvetica, arial, verdana, sans-serif" style="" size="4"><b style="">Çarpan Açıklamaları (Fiyat Hesaplama)</b></font></div><div style="font-size: 13px;"><font face="Open Sans, Helvetica Neue, helvetica, arial, verdana, sans-serif"><span style="font-size: 13px;"><br></span></font></div><div style="font-size: 13px;">Yukarıda belirtilen Tutar her bir ürün için seçilen Çarpan ile çarpılarak ürün maliyetindeki değişim hesaplanır.</div><div style="font-size: 13px;"><br></div><div style="font-size: 13px;"><u><b>Örnekler:</b></u></div><div style="font-size: 13px;"><b>Bir pazaryeri satılan ürün başına 0.12$ ücret alıyorsa</b></div><div style="font-size: 13px;"><ul><li>Tutar bölümüne 0.12 yazılır</li><li>Para Birimi olarak US Dolar seçilir</li><li>Çarpan olarak "Birim" seçilir.</li></ul><div><b>Bir pazaryeri ürün satış fiyatından %20 komisyon kesiyorsa</b></div><ul><li>Tutar bölümüne 0.20 yazılır</li><li>Para Birimi boş bırakılır (seçilse de dikkate alınmaz)</li><li>Çarpan olarak "Satış Fiyatı" seçilir</li></ul></div><div style="font-size: 13px;"><b><u>Sistemde tanımlı çarpanlar aşağıda olduğu gibidir:</u></b></div><div style="font-size: 13px;"><b>Desi 3000:</b> Birim hava desisi başına maliyet.<br></div><div style="font-size: 13px;"><font face="Open Sans, Helvetica Neue, helvetica, arial, verdana, sans-serif"><span style="font-size: 13px;"><b>Desi 5000:</b> Birim kara desisi başına maliyet.</span></font></div><div style="font-size: 13px;"><font face="Open Sans, Helvetica Neue, helvetica, arial, verdana, sans-serif"><span style="font-size: 13px;"><b>Birim:</b> Her bir ürün başına maliyet.</span></font></div><div style="font-size: 13px;"><font face="Open Sans, Helvetica Neue, helvetica, arial, verdana, sans-serif"><span style="font-size: 13px;"><b>Koli:</b> Birim koli başına maliyet.</span></font></div><div style="font-size: 13px;"><font face="Open Sans, Helvetica Neue, helvetica, arial, verdana, sans-serif"><span style="font-size: 13px;"><b>Palet:</b> Birim palet başına maliyet.</span></font></div><div style="font-size: 13px;"><font face="Open Sans, Helvetica Neue, helvetica, arial, verdana, sans-serif"><span style="font-size: 13px;"><b>Konteyner:</b> Birim konteyner başına maliyet.</span></font></div><div style="font-size: 13px;"><font face="Open Sans, Helvetica Neue, helvetica, arial, verdana, sans-serif"><span style="font-size: 13px;"><b>Kategori: </b>Kategoriye özel gümrük vb maliyet. <i>(hazır değil)</i></span></font></div><div style="font-size: 13px;"><b>Beyan Değeri:</b> Gümrük işlemleri için beyan edilen değer.<br></div><div style="font-size: 13px;"><font face="Open Sans, Helvetica Neue, helvetica, arial, verdana, sans-serif"><span style="font-size: 13px;"><b>Satış Fiyatı:</b> Ürünün nihai satış fiyatı</span></font></div>',
             'renderingClass' => '',
             'renderingData' => '',
             'border' => false,
          )),
          1 => 
          \Pimcore\Model\DataObject\ClassDefinition\Layout\Fieldcontainer::__set_state(array(
             'name' => 'Field Container',
             'type' => NULL,
             'region' => 'east',
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
                 'name' => 'pricingValue',
                 'title' => 'Tutar',
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
              1 => 
              \Pimcore\Model\DataObject\ClassDefinition\Data\Select::__set_state(array(
                 'name' => 'currency',
                 'title' => 'Para Birimi',
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
                 'defaultValue' => NULL,
                 'columnLength' => 190,
                 'dynamicOptions' => true,
                 'defaultValueGenerator' => '',
                 'width' => '',
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
             'fieldLabel' => 'Çarpan Başına İlave Maliyet',
             'labelWidth' => 8,
             'labelAlign' => 'top',
          )),
          2 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Select::__set_state(array(
             'name' => 'pricingType',
             'title' => 'Çarpan',
             'tooltip' => '',
             'mandatory' => true,
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
             'defaultValue' => 'unit',
             'columnLength' => 190,
             'dynamicOptions' => true,
             'defaultValueGenerator' => '',
             'width' => '',
             'optionsProviderType' => 'class',
             'optionsProviderClass' => 'App\\Select\\CostPriceFactors',
             'optionsProviderData' => '',
          )),
          3 => 
          \Pimcore\Model\DataObject\ClassDefinition\Data\Textarea::__set_state(array(
             'name' => 'nodeDescription',
             'title' => 'Düğüm Açıklaması (varsa)',
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
             'height' => 100,
             'width' => 300,
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
   'icon' => '/custom/pricenode.svg',
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
