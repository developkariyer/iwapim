<?php

namespace App\Utils;

use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\DataObject\VariantProduct;

use App\Utils\Utility;

class EtsyConnector implements MarketplaceConnectorInterface
{

    private $marketplace = null;
    private $listings = [];

    public function __construct(Marketplace $marketplace)
    {
        if (!$marketplace instanceof Marketplace ||
            !$marketplace->getPublished() ||
            $marketplace->getMarketplaceType() !== 'Etsy'
        ) {
            throw new \Exception("Marketplace is not published, is not Etsy or credentials are empty");
        }
        $this->marketplace = $marketplace;
    }

    public function download($forceDownload = false)
    {
        $filename = 'tmp/'.urlencode($this->marketplace->getShopId()).'.json';
        $this->listings = (file_exists($filename)) ? json_decode(file_get_contents($filename), true) : [];
        return count($this->listings);
    }

    public function downloadOrders()
    {

    }

    private function getImage($listing) {
        return null;
    }

    private function getAttributes($listing) {
        if (!empty($listing['property_values'])) {
            return implode(
                '_',
                array_map(function($element) {
                        $values = implode('-', array_map(function($value) {
                            return str_replace(' ', '', $value);
                        }, $element['values']));
                        return $values;
                    }, $listing['property_values'])
            );
        }
        return '';
    }

    private function getSalePrice($listing, $type='exists') {
        if (!empty($listing['offerings']) && !empty($listing['offerings'][0]['price'])) {
            return match ($type) {
                'price' => number_format($listing['offerings'][0]['price']['amount'] / 100, 2, '.', ''), 
                'currency'=> $listing['offerings'][0]['price']['currency_code'],
                'exists' => true,
            };
        }
        return '';
    }

    public function import($updateFlag, $importFlag)
    {
        if (empty($this->listings)) {
            echo "Nothing to import\n";
        }
        $marketplaceFolder = Utility::checkSetPath(
            Utility::sanitizeVariable($this->marketplace->getKey(), 190),
            Utility::checkSetPath('Pazaryerleri')
        );
        $total = count($this->listings);
        $index = 0;
        foreach ($this->listings as $mainListing) {
            echo "($index/$total) Processing Listing {$mainListing['listing_id']}:{$mainListing['title']} ...";
            $parent = Utility::checkSetPath(
                Utility::sanitizeVariable($mainListing['shop_section_id'] ?? 'Tasnif-Edilmemiş'),
                $marketplaceFolder
            );
            if (!empty($mainListing['title'])) {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable($mainListing['title']),
                    $parent
                );    
            }
            $parentResponseJson = $mainListing;
            if (isset($parentResponseJson['inventory'])) {
                unset($parentResponseJson['inventory']);
            }
            foreach ($mainListing['inventory'] as $listing) {
                VariantProduct::addUpdateVariant(
                    variant: [
                        'imageUrl' => $this->getImage($listing),
                        'urlLink' => null,
                        'salePrice' => $this->getSalePrice($listing, 'price'),
                        'saleCurrency' => $this->getSalePrice($listing, 'currency'),
                        'attributes' => $this->getAttributes($listing),
                        'title' => ($mainListing['title'] ?? '').($this->getAttributes($listing)),
                        'uniqueMarketplaceId' => $listing['product_id'] ?? '',
                        'apiResponseJson' => json_encode($listing, JSON_PRETTY_PRINT),
                        'parentResponseJson' => json_encode($parentResponseJson, JSON_PRETTY_PRINT),
                        'published' => !((bool) $listing['is_deleted'] ?? false),
                    ],
                    importFlag: $importFlag,
                    updateFlag: $updateFlag,
                    marketplace: $this->marketplace,
                    parent: $parent
                );
                echo "v";
            }
            echo "OK\n";
            $index++;
        }
    }

}

/*
### **Areas of Concern and Recommendations**

1. **Incomplete Error Handling in Constructor:**
   - The constructor checks if the `Marketplace` is of type `Etsy` and if it is published, but it does not check if the necessary credentials (like API keys or tokens) are available.
     - **Recommendation:** Add checks to ensure that the required credentials are available and valid. If they are missing or invalid, throw an exception.

2. **Unvalidated JSON Decoding:**
   - The method `download()` decodes a JSON file without checking for errors. If the JSON file is malformed or corrupt, it could cause issues down the line.
     - **Recommendation:** After decoding the JSON with `json_decode`, check for errors using `json_last_error()` and handle any issues accordingly. For example:
       ```php
       $this->listings = json_decode(file_get_contents($filename), true);
       if (json_last_error() !== JSON_ERROR_NONE) {
           throw new \Exception('Error decoding JSON: ' . json_last_error_msg());
       }
       ```

3. **Empty `downloadOrders()` Method:**
   - The `downloadOrders()` method is currently empty, which might indicate incomplete implementation.
     - **Recommendation:** Either implement the method or remove it if it's not going to be used. If it is a placeholder for future development, add a comment to indicate this.

4. **Inconsistent Error Handling:**
   - The `import()` method uses `echo` to report errors or issues, which is not suitable for a production environment.
     - **Recommendation:** Replace `echo` statements with proper logging or exception handling to ensure that errors are handled consistently and can be traced.

5. **Potential Issues with `match` Expression:**
   - The `match` expression in `getSalePrice()` assumes that the `listing['offerings'][0]['price']` is always present. If this key is missing, it could result in a notice or undefined behavior.
     - **Recommendation:** Add a check before using the `match` expression to ensure that `listing['offerings'][0]['price']` exists. Alternatively, handle this case in the `default` branch of the `match` expression.

6. **Hardcoded File Paths:**
   - The file path in the `download()` method (`'tmp/' . urlencode($this->marketplace->getShopId()) . '.json'`) is hardcoded, which could lead to issues if the directory structure changes or if multiple processes access the same file.
     - **Recommendation:** Make the file path configurable or use a more dynamic approach to generate unique file names, such as including a timestamp or a unique identifier.

7. **Lack of Robustness in `getImage()`:**
   - The `getImage()` method always returns `null`, which may not be its intended functionality.
     - **Recommendation:** If the method is supposed to fetch or generate an image URL, implement the logic. If it is not needed, remove the method to avoid confusion.

8. **File Operations Without Error Handling:**
   - The method `download()` reads from a file without checking if the file operation is successful. If the file does not exist or cannot be read, this could cause issues.
     - **Recommendation:** Add checks to ensure that the file exists and is readable before attempting to read it. Handle any file-related errors gracefully.

9. **No API Interaction in `download()` Method:**
   - The `download()` method only reads from a local file and does not interact with the Etsy API, which might be an incomplete or incorrect implementation, depending on the intended functionality.
     - **Recommendation:** If the method is supposed to download data from Etsy, implement the necessary API interactions. Otherwise, clarify the purpose of the method in comments or documentation.

10. **Hardcoded JSON Encoding Options:**
    - The JSON encoding options in `json_encode` (e.g., `JSON_PRETTY_PRINT`) are hardcoded.
      - **Recommendation:** Consider whether these options are necessary for all use cases, and if not, allow them to be configurable.

11. **Potential Undefined Indexes:**
    - The code frequently accesses array elements (e.g., `listing['inventory']`, `listing['offerings'][0]['price']`) without checking if they exist, which could lead to notices or errors.
      - **Recommendation:** Use `isset()` or `array_key_exists()` to ensure that the array elements exist before accessing them.

12. **Overuse of `null` Values:**
    - The code uses `null` values (e.g., for `$this->listings` and `urlLink`) in a way that might lead to confusion or bugs if not handled properly.
      - **Recommendation:** Use more meaningful default values where possible, and clearly document when and why `null` is an acceptable value.

Addressing these issues will improve the code's robustness, security, and maintainability.
*/