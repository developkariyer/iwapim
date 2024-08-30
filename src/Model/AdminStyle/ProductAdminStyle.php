<?php

namespace App\Model\AdminStyle;

use App\Website\Tool\ForceInheritance;
use Pimcore\Model\Element\AdminStyle;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\VariantProduct;

class ProductAdminStyle extends AdminStyle
{
    /** @var ElementInterface */
    protected $element;

    public function __construct($element)
    {
        parent::__construct($element);

        $this->element = $element;

        if ($element instanceof Product) {
            switch ($element->level()) {
                case 0:
                    $this->elementIcon = '/custom/navyobject.svg';
                    break;
                case 1:
                    $this->elementIcon = (count($element->getListingItems())) ? '/custom/deployment.svg' : '/custom/object.svg';
                    break;
                default:
            }
        }
        if ($element instanceof VariantProduct) {
            $this->elementIcon = (count($this->element->getMainProduct())) ? '/bundles/pimcoreadmin/img/flat-color-icons/accept_database.svg' : '/bundles/pimcoreadmin/img/flat-color-icons/list.svg';
        }
    }

    public function getElementQtipConfig(): ?array
    {
        if ($this->element instanceof Product) {
            $config = parent::getElementQtipConfig();
            $config['title'] = "{$this->element->getId()}: {$this->element->getName()}";
            $shopifyVariations = $total = count($this->element->getListingItems());
            foreach ($this->element->getChildren() as $child) {
                if ($child instanceof Product) {
                    $shopifyVariations += count($child->getListingItems());
                }
            }
            $config['text'] = "$total/$shopifyVariations listing bağlı<br>";
            $image = $this->element->getInheritedField('image');
            if ($image) {
                $config["text"] .= "<img src='{$image->getThumbnail()->getPath()}' style='max-width: 100%; height: 100px; background-color: #f0f0f0;'>";
            }
            $album = $this->element->getInheritedField('album');
            foreach ($album as $asset) {
                if (!$asset) {
                    continue;
                }
                $image = $asset->getImage();
                if ($image) {
                    $config['text'] .= "<img src='{$image->getThumbnail()->getPath()}' style='max-width: 100%; height: 100px; background-color: #f0f0f0;'>";
                    break;
                }
            }
            $imageUrl = $this->element->getInheritedField('imageUrl');
            if ($imageUrl) {
                $config['text'] .= "<img src='{$imageUrl->getUrl()}' style='max-width: 100%; height: 100px; background-color: #f0f0f0;'>";
            }
            return $config;
        }
        if ($this->element instanceof VariantProduct) {
            $config = parent::getElementQtipConfig();
            if ($this->element->getImageUrl()) {
                $config['text'] = "<br><img src='{$this->element->getImageUrl()->getUrl()}' style='max-width: 100%; height: 100px; background-color: #f0f0f0;'>";
            }
            return $config;
        }
        return parent::getElementQtipConfig();
    }
}
/*
### **Areas of Concern and Recommendations**

1. **Unnecessary Property Assignment in Constructor:**
   - The property `$element` is assigned twice in the constructor: once in the `parent::__construct($element)` call and again immediately after. This is redundant and could be avoided.
     - **Recommendation:** Remove the redundant assignment of `$this->element` in the constructor, as it is already set by the parent class.

2. **Hardcoded Paths for Icons:**
   - The code uses hardcoded file paths for icons (e.g., `'/custom/navyobject.svg'`). This approach is inflexible and can lead to issues if the paths change or if the project structure varies between environments.
     - **Recommendation:** Consider defining these paths in a configuration file or as constants. This makes it easier to update the paths if necessary and improves maintainability.

3. **Use of Count Function on Potentially Null Values:**
   - The method calls `count()` on potentially null values such as `getBundleItems()`, `getListingItems()`, and `getMainProduct()`. If these methods return `null`, PHP will raise a warning.
     - **Recommendation:** Ensure that the methods return arrays or handle the possibility of `null` before calling `count()`. For example:
       ```php
       $items = $element->getBundleItems() ?: [];
       if (count($items)) {
           // Do something
       }
       ```

4. **Potential Performance Issues with Deeply Nested Loops:**
   - The `getElementQtipConfig()` method uses nested loops to iterate over the product's children and their children to count listing items. This could lead to performance issues if the product hierarchy is deep or large.
     - **Recommendation:** Optimize the loop by using a more efficient traversal method or by caching the results of previous calculations. Consider breaking out of the loop early if the required data is found.

5. **Unconditional Image Inheritance:**
   - The `getElementQtipConfig()` method inherits images without checking if the inheritance is necessary or valid. This could lead to unexpected results if images are inherited incorrectly.
     - **Recommendation:** Add checks to ensure that the inherited images are relevant and correct for the current context. If not, provide a fallback mechanism.

6. **Potential HTML Injection Risk:**
   - The code constructs HTML content by directly embedding variables like `$imageUrl->getUrl()` and `$image->getThumbnail()->getPath()` without escaping them. If any of these fields contain unexpected or malicious content, it could lead to an XSS vulnerability.
     - **Recommendation:** Always sanitize or escape the variables before embedding them in HTML. For example:
       ```php
       $config['text'] .= "<img src='" . htmlspecialchars($imageUrl->getUrl(), ENT_QUOTES, 'UTF-8') . "' style='max-width: 100%; height: 100px; background-color: #f0f0f0;'>";
       ```

7. **Missing Error Handling for Image Retrieval:**
   - The code assumes that methods like `getThumbnail()` and `getUrl()` always succeed. If these methods fail or return `null`, it could lead to errors or unexpected behavior.
     - **Recommendation:** Add error handling or checks to ensure that these methods return valid data before attempting to use it.

8. **Limited Customization of Qtip Configurations:**
   - The method `getElementQtipConfig()` customizes the Qtip config but does not allow for easy extension or further customization without modifying the method directly.
     - **Recommendation:** Consider providing hooks or methods that allow for further customization of the Qtip configuration without needing to modify the core method.

9. **No Type Declarations for Constructor Parameter:**
   - The constructor parameter `$element` is not type-hinted, which reduces the clarity of the expected input and makes the code more prone to errors.
     - **Recommendation:** Add type declarations to the constructor parameter to enforce the expected type. For example:
       ```php
       public function __construct(ElementInterface $element)
       ```

10. **Lack of Documentation:**
    - The class and its methods lack DocBlocks or comments explaining their purpose and functionality. This can make the code harder to understand and maintain.
      - **Recommendation:** Add DocBlocks to the class and its methods to explain their purpose, parameters, return types, and any important details. This will improve code readability and maintainability.

By addressing these issues, the code will be more robust, secure, and maintainable, particularly in handling user input, managing performance, and ensuring flexibility for future changes.
*/