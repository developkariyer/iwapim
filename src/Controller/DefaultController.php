<?php

namespace App\Controller;

//use Pimcore\Bundle\AdminBundle\Controller\Admin\LoginController;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\DataObject\ProductClassOptionsProvider;
use Pimcore\Model\DataObject\Product\Listing;
use Pimcore\Model\DataObject\Brand\Listing as BrandListing;
use Pimcore\Model\DataObject\PricingNode\Listing as PricingNodeListing;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\Select;
use Pimcore\Cache;

class DefaultController extends FrontendController
{
    /**
     * @Route("/", name="default_homepage")
     */
    public function defaultAction(Request $request): Response
    {

        return $this->render(
            'iwa/default.html.twig', 
            [
                'categories' => $this->getFromCache('productClasses'),
                'user' => $this->getUserDetails(),
                'pricingtypes' => $this->getFromCache('pricingNodes'),
                'brands' => $this->getFromCache('brands'),

                'messages' => $this->getMessages(),
                'costtypes' => $this->getFromCache('costNodes'),
            ]
        );
    }    
    
    /**
     * @Route("/login", name="admin_login")
     */
    public function loginAction(): Response
    {
        //return $this->forward(LoginController::class.'::loginCheckAction');
        return $this->render('iwa/login.html.twig');
    }

    /* Private functions */

    private function getFromCache($key)
    {
        if (!in_array($key, ['productClasses', 'brands', 'pricingNodes', 'costNodes'])) {
            throw new \Exception('Invalid cache key');
        }
        $options = Cache::load($key);
        if ($options === false) {
            $options = $this->$key();
            Cache::save($options, $key);
        }
        return $options;
    }

    private function productClasses()
    {
        $options = (new ProductClassOptionsProvider())->getOptions([]);
        foreach ($options as $key=>$option) {
            $products = new Listing();
            $products->setObjectTypes([DataObject::OBJECT_TYPE_OBJECT]);
            $products->setCondition('productClass = ?', [$option['value']]);
            $options[$key]['productCount'] = $products->count();
        }
        return $options;
    }

    private function getMessages()
    {
        return [
            [
                'title' => 'Yeni Ürün Eklendi',
                'text' => 'Yeni ürün eklendi. Ürün kodu: 123456',
                'time' => '2 saat önce',
            ],
        ];
    }

    private function getUserDetails()
    {
        return [
            'name' => 'Umut IWA',
            'role' => 'Danışman',
        ];
    }

    private function brands()
    {
        $options = [];
        $brands = new BrandListing();
        $brands->setOrderKey('order');
        $brands->setOrder('asc');
        foreach ($brands as $brand) {
            $options[] = [
                'key' => $brand->getKey(),
                'id' => $brand->getId(),
            ];
        }
        return $options;
    }

    private function pricingNodes()
    {
        $className = 'PricingNode';
        $classDefinition = ClassDefinition::getByName($className);
        if ($classDefinition instanceof ClassDefinition) {
            $fieldName = 'nodeType'; 
            $fieldDefinition = $classDefinition->getFieldDefinition($fieldName);
            if ($fieldDefinition instanceof Select) {
                $options = $fieldDefinition->getOptions();
                foreach ($options as $key=>$option) {
                    $products = new PricingNodeListing();
                    $products->setObjectTypes([DataObject::OBJECT_TYPE_OBJECT]);
                    $products->setCondition('nodeType = ?', [$option['key']]);
                    $options[$key]['productCount'] = $products->count();        
                }
            }
        }
        return $options ?? [];
    }

    private function costNodes()
    {
        $folderPath = '/Ayarlar/Maliyetler/Üretim/';
        $folder = DataObject::getByPath($folderPath);
        $options = [];
        if ($folder instanceof DataObject\Folder) {
            $children = $folder->getChildren();
            foreach ($children as $child) {
                $costs = $child->getChildren();
                $options[] = [
                    'key' => $child->getKey(),
                    'id' => $child->getId(),
                    'productCount' => count($costs),
                ];
            }
        }
        return $options;
    }
}
