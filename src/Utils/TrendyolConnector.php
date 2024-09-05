<?php

namespace App\Utils;

use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\DataObject\VariantProduct;

use App\Utils\Utility;

class TrendyolConnector implements MarketplaceConnectorInterface
{

    private $marketplace = null;
    private $listings = [];

    public function __construct(Marketplace $marketplace)
    {
        if (!$marketplace instanceof Marketplace ||
            !$marketplace->getPublished() ||
            $marketplace->getMarketplaceType() !== 'Trendyol' ||
            empty($marketplace->getTrendyolApiKey()) ||
            empty($marketplace->getTrendyolApiSecret()) ||
            empty($marketplace->getTrendyolSellerId()) ||
            empty($marketplace->getTrendyolToken())
        ) {
            throw new \Exception("Marketplace is not published, is not Trendyol or credentials are empty");
        }
        $this->marketplace = $marketplace;
    }

    public function download($forceDownload = false)
    {
        $filename = 'tmp/'.urlencode($this->marketplace->getKey()).'.json';
        if (!$forceDownload && file_exists($filename) && filemtime($filename) > time() - 86400) {
            $this->listings = json_decode(file_get_contents($filename), true);
            echo "Using cached data ";
        } else {
            $apiUrl = "https://api.trendyol.com/sapigw/suppliers/{$this->marketplace->getTrendyolSellerId()}/products?approved=true";
            $page = 0;
            $this->listings = [];
            do {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "$apiUrl&page=$page");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Authorization: Basic {$this->marketplace->getTrendyolToken()}",
                ]);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpCode !== 200) {
                    echo "Error: $httpCode\n";
                    curl_close($ch);
                    break;
                }
                $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $body = substr($response, $headerSize);
                $data = json_decode($body, true);
                $products = $data['content'];
                $this->listings = array_merge($this->listings, $products);
                $page++;
                curl_close($ch);
                echo ".";
                sleep(1);                
            } while ($page <= $data['totalPages']);
            file_put_contents($filename, json_encode($this->listings));
        }
        return count($this->listings);
    }

    public function downloadInventory()
    {

    }

    public function downloadOrders()
    {
        
    }

    private function getImage($listing) {
        $image = $listing['images'][0]['url'] ?? '';
        if (!empty($image)) {
            $imageAsset = Utility::findImageByName("Trendyol_".str_replace(["https:", "/", ".", "_", "jpg"], '', $image).".jpg");
            if ($imageAsset) {
                $image = "https://mesa.iwa.web.tr/var/assets/".str_replace(" ", "%20", $imageAsset->getFullPath());
            }
            return new \Pimcore\Model\DataObject\Data\ExternalImage($image);
        }
        return null;
    }

    private function getUrlLink($listing) {
        $l = new Link();
        $l->setPath($listing['productUrl'] ?? '');
        return $l;
    }

    private function getAttributes($listing) {
        if (!empty($listing['attributes'])) {
            $values = array_filter(array_map(function($value) {
                return str_replace(' ', '', $value);
            }, array_column($listing['attributes'], 'attributeValue')));
            if (!empty($values)) {
                return implode('-', $values);
            }
        }
        return '';
    }

    private function getPublished($listing)
    {
        if (!isset($listing['archived'])) {
            return false;
        }
        return (bool) !$listing['archived'];
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
        foreach ($this->listings as $listing) {
            echo "($index/$total) Processing Listing {$listing['barcode']}:{$listing['title']} ...";
            $path = Utility::sanitizeVariable($listing['categoryName'] ?? 'Tasnif-Edilmemiş');
            $parent = Utility::checkSetPath($path, $marketplaceFolder);
            if ($listing['productMainId']) {
                $parent = Utility::checkSetPath(Utility::sanitizeVariable($listing['productMainId']), $parent);
            }
            VariantProduct::addUpdateVariant(
                variant: [
                    'imageUrl' => $this->getImage($listing),
                    'urlLink' => $this->getUrlLink($listing),
                    'salePrice' => $listing['salePrice'] ?? 0,
                    'saleCurrency' => 'TL',
                    'title' => $listing['title'] ?? '',
                    'attributes' => $this->getAttributes($listing),
                    'uniqueMarketplaceId' => $listing['id'] ?? '',
                    'apiResponseJson' => json_encode($listing, JSON_PRETTY_PRINT),
                    'published' => $this->getPublished($listing),
                ],
                importFlag: $importFlag,
                updateFlag: $updateFlag,
                marketplace: $this->marketplace,
                parent: $parent
            );
            echo "OK\n";
            $index++;
        }
    }

}


/*
### **Areas of Concern and Recommendations**

1. **Uncaught `curl_exec` Errors:**
   - The code does not check if `curl_exec($ch)` returns `false`. If the curl execution fails, this could lead to an undefined or empty response, which might cause further issues.
     - **Recommendation:** Add error handling for `curl_exec($ch)` to catch and handle cases where the curl operation fails. For example:
       ```php
       $response = curl_exec($ch);
       if ($response === false) {
           echo 'Curl error: ' . curl_error($ch);
           curl_close($ch);
           break;
       }
       ```

2. **Insecure Token Usage:**
   - The Trendyol API token is passed directly in the `Authorization` header. If the API request or the script is not executed in a secure environment, this could expose sensitive credentials.
     - **Recommendation:** Ensure that the connection is secure (over HTTPS) and consider storing sensitive tokens in a more secure way, such as environment variables, and avoid hardcoding them directly in the code.

3. **Hardcoded API Endpoint:**
   - The API URL is hardcoded with the Trendyol endpoint, which limits flexibility.
     - **Recommendation:** Consider making the API URL configurable, possibly through environment variables or configuration files, to easily accommodate changes or different environments (e.g., staging, production).

4. **Potential Infinite Loop:**
   - The `do-while` loop in the `download` method uses `$page <= $data['totalPages']` as the exit condition, but if there is any issue with `totalPages` not being correctly retrieved or if `totalPages` is dynamically updated, this could cause an infinite loop.
     - **Recommendation:** Implement an additional safety mechanism, such as a maximum number of iterations, to avoid potential infinite loops.

5. **Inconsistent Error Handling:**
   - The error handling in the `download` method is inconsistent, relying on `echo` statements for logging errors instead of a structured approach.
     - **Recommendation:** Implement a consistent error-handling strategy, using exceptions or a logging framework, rather than echoing errors to the console.

6. **Missing Type Declarations:**
   - The class properties and method parameters lack type declarations, which can lead to type-related bugs and makes the code harder to understand.
     - **Recommendation:** Use type hints and return type declarations for properties and method parameters to improve code robustness and readability.

7. **File Handling and Security:**
   - The temporary file is stored in the `tmp/` directory, which may not be secure, and no validation or sanitization of the file path is performed.
     - **Recommendation:** Ensure that the temporary files are stored in a secure, non-public directory, and validate or sanitize file paths to avoid directory traversal vulnerabilities.

8. **Lack of Pagination Handling in API Calls:**
   - The pagination logic is very basic and relies on the assumption that `$data['totalPages']` is always accurate and available.
     - **Recommendation:** Improve the pagination logic by checking for other pagination-related metadata (if available) from the API response to ensure robust pagination handling.

9. **`json_decode` Without Error Handling:**
   - The `json_decode` function is used without checking for errors, which can lead to issues if the JSON is malformed or the decoding fails.
     - **Recommendation:** After decoding the JSON, check for errors using `json_last_error()` and handle any potential issues appropriately.

10. **Use of Magic Strings:**
    - The code contains several magic strings, such as hardcoded field names and API URLs, which can make the code more error-prone and harder to maintain.
      - **Recommendation:** Replace magic strings with constants or configuration values to improve maintainability and reduce the risk of errors.

11. **No Rate Limiting or Retry Logic:**
    - The API requests do not account for potential rate limiting by Trendyol, and there is no retry logic in case of transient failures.
      - **Recommendation:** Implement rate limiting and retry logic to handle cases where the API may throttle requests or encounter temporary issues.

12. **Potential Overwriting of Files:**
    - The method `download()` writes to a file with a fixed name, which could lead to overwriting issues if multiple processes run concurrently.
      - **Recommendation:** Use unique file names, possibly including a timestamp or a UUID, to avoid file overwriting issues.

13. **Overly Broad Exception Handling in Constructor:**
    - The constructor throws a general `\Exception` if the marketplace conditions are not met, but it might be more appropriate to use a custom exception or provide more granular error messages.
      - **Recommendation:** Consider using a more specific exception type, such as `InvalidMarketplaceException`, to make error handling more precise and understandable.

By addressing these issues, the code will become more secure, robust, and maintainable.
*/