# Installation of the Opensearch Client Bundle

:::info

 This bundle is only supported on Pimcore Core Framework 11.

:::

 ## Bundle Installation

To install the Opensearch Client Bundle, follow the three steps below:

1) Install the required dependencies:

```bash
composer require pimcore/opensearch-client
```

2) This bundle is a standard symfony bundle. If not required and activated by another bundle, it can be enabled by adding it to the `bundles.php` of your application.

```php
use Pimcore\Bundle\OpenSearchClientBundle\PimcoreOpenSearchClientBundle;
// ...
return [
    // ...
    PimcoreOpenSearchClientBundle::class => ['all' => true],
    // ...
];  
```