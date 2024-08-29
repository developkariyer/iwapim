<?php

namespace App\EventListener;

use App\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\CustomLayout;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Serial;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

use Pimcore\Event\Model\DataObjectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Pimcore\Cache;
use Symfony\Component\EventDispatcher\GenericEvent;

class DataObjectListener implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'pimcore.dataobject.preDelete' => 'onPreDelete',
            'pimcore.dataobject.preAdd' => 'onPreAdd',
            'pimcore.dataobject.preUpdate' => 'onPreUpdate',
            'pimcore.dataobject.postUpdate' => 'onPostUpdate',
            'pimcore.dataobject.postLoad' => 'onPostLoad',
            'pimcore.admin.resolve.elementAdminStyle' => 'onResolveElementAdminStyle',
//            'pimcore.admin.dataobject.get.preSendData' => 'onPreSendData',
        ];
    }

    public function onPreSendData(GenericEvent $event)
    {
        $object = $event->getArgument('object');
        if ($object instanceof Product) {
            $data = $event->getArgument('data');
            file_put_contents('/var/www/iwapim/tmp/data.json', json_encode($data));
            file_put_contents('/var/www/iwapim/tmp/data.txt', print_r($data, true));
        }
    }

    public function onPreDelete(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if ($object instanceof Folder) {
            $parent = $object->getParent();
            if ($object->getKey() === 'Ayarlar' || ($parent && $parent->getKey() === 'Ayarlar')) {
                throw new \Exception('Ayarlar klasörü ve altındaki ana klasörler silinemez');
            }
        }
    }

    public function onResolveElementAdminStyle(\Pimcore\Bundle\AdminBundle\Event\ElementAdminStyleEvent $event)
    {
        $object = $event->getElement();
        if (
            $object instanceof Product || 
            $object instanceof VariantProduct
        ) {
            $event->setAdminStyle(new \App\Model\AdminStyle\ProductAdminStyle($object));
        }
    }

    /**
     * Called before initializing a new object
     * Used to set productCode, variationColor and variationSize values
     * 
     * @param DataObjectEvent $event
     */
    public function onPreAdd(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if ($object instanceof Product) {
            $object->checkProductCode();
        }
    }

    /**
     * Called before saving an object to database
     * Used for setting object folder
     * Used for setting Iwasku when set active
     * 
     * $param DataObjectEvent $event
     */
    public function onPreUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if ($object instanceof Product) {
            $object->checkIwasku();
            $object->checkProductCode();
            $object->checkProductIdentifier();
            $object->checkKey();
            if ($object->getParent() instanceof Product) {
                $object->nullify();
            }
        }
        if ($object instanceof Serial) {
            $object->checkLabel();
        }
    }

    public function onPostUpdate(DataObjectEvent $event)
    {
        $object = $event->getObject();
        if ($object instanceof Product) {
            if (!$object->getParent() instanceof Product) {
                $object->checkVariations();
            }
            $object->checkAssetFolders();
        }
    }

    private static function traverseProducts($object)
    {
        $listingItems = $object->getListingItems();
        foreach ($listingItems as $listingItem) {
            if (($listingItem instanceof VariantProduct)) {
                if ($listingItem->getImageUrl() instanceof \Pimcore\Model\DataObject\Data\ExternalImage) {
                    return $listingItem->getImageUrl()->getUrl();
                }
            }
        }
        $children = $object->getChildren();
        foreach ($children as $child) {
            if ($child instanceof Product) {
                $image = self::traverseProducts($child);
                if (!empty($image)) {
                    return $image;
                }
            }
        }
        return "";
    }

    public function onPostLoad(DataObjectEvent $event)
    {
        $object = $event->getObject();
        $image_url = '';
        if ($object instanceof Product) {
            $image_url = self::traverseProducts($object);
            if (!empty($image_url)) {
                $object->setImageUrl(new \Pimcore\Model\DataObject\Data\ExternalImage($image_url));
            }
            if (!$object->getParent() instanceof Product) {
                [$sizes, $colors] = $object->listVariations();
                $object->setVariationSizeList(implode("\n", $sizes));
                $object->setVariationColorList(implode("\n", $colors));
            } else {
                $object->setVariationSizeList('');
                $object->setVariationColorList('');
            }
        }
    }

}

/*
### **Areas of Concern and Recommendations**

1. **Use of `file_put_contents` for Debugging in `onPreSendData`:**
   - The method `onPreSendData` writes data to files in the `/var/www/iwapim/tmp/` directory using `file_put_contents`. This practice is not suitable for production environments as it can lead to performance issues and potential security risks if sensitive data is exposed.
     - **Recommendation:** Remove these `file_put_contents` calls or replace them with proper logging using a logging library like Monolog. Ensure sensitive data is never written to publicly accessible locations.

2. **Lack of Validation in `onPreDelete`:**
   - The `onPreDelete` method throws an exception if a specific folder is being deleted, but it only checks the folder's name. This approach might be fragile, as folder names can change or be duplicated.
     - **Recommendation:** Instead of relying on folder names, use a more robust identification method, such as checking a unique identifier or ensuring the folder's path matches the intended protected location.

3. **Potential Performance Issues with Recursive Methods:**
   - The methods `traverseProducts` and `onPostLoad` use recursion to traverse product variations and children. This could lead to performance issues if the product hierarchy is deep or contains a large number of children.
     - **Recommendation:** Optimize the recursion by introducing limits or breaking out of the loop as soon as the necessary data is found. Consider caching results or using an iterative approach if the product structure is known to be large.

4. **Inconsistent Error Handling:**
   - The code relies on exceptions for error handling but does not consistently handle these exceptions across different methods.
     - **Recommendation:** Implement a consistent error-handling strategy, ensuring that exceptions are caught and managed appropriately, especially in event listeners where unexpected failures can disrupt the application flow.

5. **Potential Infinite Loop or Excessive Recursion:**
   - The `traverseProducts` method, when combined with `onPostLoad`, could potentially cause an infinite loop or excessive recursion if the product hierarchy contains cycles or is very deep.
     - **Recommendation:** Introduce checks to prevent infinite loops, such as maintaining a list of visited nodes or limiting the recursion depth.

6. **No Error Handling for `setImageUrl` in `onPostLoad`:**
   - The `onPostLoad` method sets the `imageUrl` without checking if the `ExternalImage` object was created successfully. If the URL is invalid or if there are issues creating the `ExternalImage` object, this could lead to further errors.
     - **Recommendation:** Add error handling around the creation and assignment of `ExternalImage` objects to ensure that invalid data does not cause issues downstream.

7. **Redundant or Confusing Logic in `onPostLoad`:**
   - The method `onPostLoad` sets variation size and color lists but immediately clears them if the product has a parent. This logic could be simplified or clarified to improve readability.
     - **Recommendation:** Refactor the conditional logic to make it more intuitive, possibly by separating concerns into different methods or using early returns to reduce complexity.

8. **Lack of Type Declarations and DocBlocks:**
   - The methods lack type hints and comprehensive DocBlocks, which can make the code harder to understand and maintain.
     - **Recommendation:** Add type hints for method parameters and return types, and include DocBlocks to describe the purpose of each method and the expected data types.

9. **Overuse of Static Methods:**
   - The use of static methods like `traverseProducts` can lead to issues with testability and maintainability, especially if these methods rely on external state or services.
     - **Recommendation:** Consider making these methods instance methods or refactoring them into a service class that can be injected where needed. This will improve testability and adherence to object-oriented principles.

10. **No Validation for Admin Style Resolution in `onResolveElementAdminStyle`:**
    - The method `onResolveElementAdminStyle` sets a custom admin style without validating whether the style object is valid or appropriate for the given context.
      - **Recommendation:** Add validation to ensure that the custom admin style is appropriate for the object and context. This could prevent unintended side effects or errors in the admin interface.

11. **Potentially Inefficient Use of `Cache`:**
    - Although the `Cache` class is imported, it is not used in the code. If caching is intended, it should be implemented properly to optimize performance.
      - **Recommendation:** Either remove the unused `Cache` import or implement caching where appropriate to improve performance and reduce redundant calculations or data retrievals.

By addressing these issues, the code will become more reliable, maintainable, and efficient, especially in a production environment.
*/