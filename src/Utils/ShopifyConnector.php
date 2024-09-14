<?php

namespace App\Utils;

use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Data\Link;
use Pimcore\Model\DataObject\VariantProduct;
use App\Command\CacheImagesCommand;

use App\Utils\Utility;

class ShopifyConnector implements MarketplaceConnectorInterface
{

    private $marketplace = null;
    private $listings = [];

    public function __construct(Marketplace $marketplace)
    {
        if (!$marketplace instanceof Marketplace ||
            !$marketplace->getPublished() ||
            $marketplace->getMarketplaceType() !== 'Shopify' ||
            empty($marketplace->getAccessToken()) ||
            empty($marketplace->getApiUrl())
        ) {
            throw new \Exception("Marketplace is not published, is not Shopify or credentials are empty");
        }
        $this->marketplace = $marketplace;
        echo " initialiazed\n";
    }

    public function download($forceDownload = false)
    {
        $filename = 'tmp/'.urlencode($this->marketplace->getKey()).'.json';
        if (!$forceDownload && file_exists($filename) && filemtime($filename) > time() - 86400) {
            $this->listings = json_decode(file_get_contents($filename), true);
            echo "Using cached data\n";
        } else {
            $accessToken = $this->marketplace->getAccessToken();
            $apiUrl = $this->marketplace->getApiUrl();
            $apiVersion = '2024-01';
            $limit = 50;
            $this->listings = [];
            $url = "https://{$apiUrl}/admin/api/{$apiVersion}/products.json?limit={$limit}";
            do {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "X-Shopify-Access-Token: $accessToken"
                ]);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpCode !== 200) {
                    echo "Error: $httpCode\n";
                    curl_close($ch);
                    break;
                }
                $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $header = substr($response, 0, $headerSize);
                $body = substr($response, $headerSize);
                $data = json_decode($body, true);
                $products = $data['products'];
                $this->listings = array_merge($this->listings, $products);
                $url = null;
                if (preg_match('/<([^>]+)>;\s*rel="next"/', $header, $matches)) {
                    $url = $matches[1];
                }
                curl_close($ch);
                echo ".";
                sleep(1);
            } while ($url);
            file_put_contents($filename, json_encode($this->listings));    
        }
        return count($this->listings);
    }

    public function downloadInventory()
    {

    }

    public function downloadOrders()
    {
        $db = \Pimcore\Db::get();
        // find biggest id for this shop
        $sql = "SELECT MAX(order_id) FROM iwa_shopify_orders WHERE shopify_id = ?";
        $maxId = $db->fetchOne($sql, [$this->marketplace->getId()]);
        if (!$maxId) {
            $maxId = 0;
        }
        $accessToken = $this->marketplace->getAccessToken();
        $apiUrl = $this->marketplace->getApiUrl();
        $apiVersion = '2024-07';
        $url = "https://{$apiUrl}/admin/api/{$apiVersion}/orders.json?status=any&since_id=".($maxId+1);
        $sql = "INSERT INTO iwa_shopify_orders (shopify_id, order_id, json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE json = VALUES(json)";
        do {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "X-Shopify-Access-Token: $accessToken"
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode !== 200) {
                echo "Error: $httpCode\n";
                curl_close($ch);
                break;
            }
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            $data = json_decode($body, true);
            $orders = $data['orders'];
            try {
                $db->beginTransaction();
                foreach ($orders as $order) {
                    $db->executeUpdate($sql, [$this->marketplace->getId(), $order['id'], json_encode($order)]);
                }
                $db->commit();
            } catch (\Exception $e) {
                $db->rollBack();
                echo "Error: " . $e->getMessage() . "\n";
            }
            $url = null;
            if (preg_match('/<([^>]+)>;\s*rel="next"/', $header, $matches)) {
                $url = $matches[1];
            }
            curl_close($ch);
            echo ".".count($orders);
            sleep(1);
        } while ($url);
    }

    private static function getCachedImage($url)
    {
        $imageAsset = Utility::findImageByName(CacheImagesCommand::createUniqueFileNameFromUrl($url));
        if ($imageAsset) {
            return "https://mesa.iwa.web.tr/var/assets/".str_replace(" ", "%20", $imageAsset->getFullPath());
        }
        return $url;
    }

    private function getImage($listing, $mainListing) {
        $lastImage = "";
        $images = $mainListing['images'] ?? [];
        foreach ($images as $img) {
            if (!is_numeric($listing['image_id']) || $img['id'] === $listing['image_id']) {
                return static::getCachedImage($img['src']);
            } 
            if (empty($lastImage)) {
                $lastImage = static::getCachedImage($img['src']);
            }
        }
        if (!empty($mainListing['image']['src'])) {
            return static::getCachedImage($mainListing['image']['src']);
        }
        return $lastImage;
    }

    private function getUrlLink($listing, $mainListing)
    {
        if (!empty($mainListing['handle']) && !empty($listing['id'])) {
            $l = new Link();
            $l->setPath($this->marketplace->getMarketplaceUrl().'products/'.$mainListing['handle'].'/'.$listing['id']);
            return $l;
        }
        return null;
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
            echo "($index/$total) Processing Listing {$mainListing['id']}:{$mainListing['title']} ...";
            $parent = Utility::checkSetPath(
                Utility::sanitizeVariable($mainListing['product_type'] ?? 'Tasnif-Edilmemiş'),
                $marketplaceFolder
            );
            if (!empty($mainListing['title'])) {
                $parent = Utility::checkSetPath(
                    Utility::sanitizeVariable($mainListing['title']),
                    $parent
                );    
            }
            $parentResponseJson = $mainListing;
            if (isset($parentResponseJson['variants'])) {
                unset($parentResponseJson['variants']);
            }
            foreach ($mainListing['variants'] as $listing) {
                VariantProduct::addUpdateVariant(
                    variant: [
                        'imageUrl' => $this->getImage($listing, $mainListing),
                        'urlLink' => $this->getUrlLink($listing, $mainListing),
                        'salePrice' => $listing['price'] ?? '',
                        'saleCurrency' => $this->marketplace->getCurrency(),
                        'attributes' => $listing['title'] ?? '',
                        'title' => ($mainListing['title'] ?? '').($listing['title'] ?? ''),
                        'uniqueMarketplaceId' => $listing['id'] ?? '',
                        'apiResponseJson' => json_encode($listing),
                        'parentResponseJson' => json_encode($parentResponseJson),
                        'published' => true,
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

1. **Uncaught `curl_exec` Errors:**
   - The code does not check whether `curl_exec($ch)` returns `false`, which can happen in case of a curl error. This could lead to unexpected behavior if the response is empty or invalid.
     - **Recommendation:** Check the result of `curl_exec($ch)` and handle errors appropriately using `curl_error($ch)` to provide more detailed error handling.

2. **Hardcoded API Version and URL:**
   - The API version (`2024-01`, `2024-07`) is hardcoded, which reduces flexibility and requires code changes if the API version needs to be updated.
     - **Recommendation:** Externalize the API version into a configuration or environment variable to make it easier to update.

3. **File Handling and Security:**
   - The temporary file is stored in a publicly accessible directory (`tmp/`) without any security measures. This could be a potential security risk if the file contains sensitive information.
     - **Recommendation:** Use a more secure directory for storing temporary files, and ensure the file paths are sanitized and validated to prevent directory traversal attacks.

4. **`json_decode` Without Error Handling:**
   - The code uses `json_decode` without checking for errors, which can lead to issues if the JSON is malformed or if decoding fails.
     - **Recommendation:** Add error handling after `json_decode` by checking `json_last_error()` and handling potential decoding errors.

5. **Inconsistent Exception Handling:**
   - While the constructor throws exceptions for certain conditions, other methods like `downloadOrders()` handle errors with echo statements rather than throwing exceptions. This inconsistency can make it harder to maintain or debug.
     - **Recommendation:** Standardize error handling by either throwing exceptions or using a logging mechanism, and avoid using `echo` for error messages in production code.

6. **Blocking Code with `do-while` Loops:**
   - The `download` and `downloadOrders` methods use `do-while` loops that can block execution if the Shopify API is slow to respond, leading to potential performance bottlenecks.
     - **Recommendation:** Implement timeout settings or consider breaking down these tasks into smaller chunks with asynchronous processing if possible.

7. **Vulnerable to API Rate Limits:**
   - The code does not handle potential rate-limiting issues that might arise when making multiple requests to the Shopify API. This could lead to temporary bans or throttling by Shopify.
     - **Recommendation:** Implement logic to handle rate limits by checking response headers like `Retry-After` and pausing requests accordingly.

8. **SQL Injection Risk:**
   - The `downloadOrders()` method uses a raw SQL query with a placeholder for the `shopify_id`, which could potentially lead to SQL injection if the value is not properly validated (though it seems to be handled safely here).
     - **Recommendation:** Always ensure that the inputs to SQL queries are validated or sanitized, or better yet, use parameterized queries or an ORM to handle database interactions.

9. **Code Duplication:**
   - The logic for handling `curl` responses is duplicated in both `download` and `downloadOrders` methods.
     - **Recommendation:** Refactor the common `curl` logic into a reusable private method to reduce duplication and make the code easier to maintain.

10. **Overuse of `null` Values:**
    - The `url` is set to `null` in both `download` and `downloadOrders` methods after each loop iteration, which might not be necessary.
      - **Recommendation:** Consider whether resetting `url` to `null` is essential. If not, remove it to simplify the code.

11. **Lack of Type Declarations:**
    - The class uses untyped parameters and properties (`$marketplace`, `$listings`, etc.), which can lead to type-related bugs.
      - **Recommendation:** Use type hints and declare the types of class properties and method parameters to improve code safety and readability.

12. **Lack of Robust Logging:**
    - The code uses `echo` for logging, which is not suitable for production environments and makes it difficult to track or diagnose issues.
      - **Recommendation:** Implement a logging system to capture important events, errors, and debug information.

13. **Potential Overwrite of Files:**
    - The method `downloadOrders()` uses a fixed file path (`tmp/`), which might lead to file overwrites if multiple processes are running concurrently.
      - **Recommendation:** Use unique file names based on timestamp or a UUID to prevent overwrites and ensure each process has its own temporary file.

Addressing these issues will help improve the reliability, security, and maintainability of the code.
*/