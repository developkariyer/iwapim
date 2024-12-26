<?php

namespace App\EventListener;

use App\Model\AdminStyle\ProductAdminStyle;
use App\Model\DataObject\VariantProduct;
use Pimcore\Bundle\AdminBundle\Event\ElementAdminStyleEvent;
use Pimcore\Model\DataObject\ClassDefinition\CustomLayout;
use Pimcore\Model\DataObject\Product;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

use Pimcore\Model\DataObject\Service;

class AdminListener implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'pimcore.admin.resolve.elementAdminStyle' => 'onResolveElementAdminStyle',
            'pimcore.admin.dataobject.get.preSendData' => 'onPreSendData',
        ];
    }

    public function doModifyCustomLayouts(Product $object, GenericEvent $event): void
    {
        $level = $object->level();
        $data = $event->getArgument('data');
        if (empty($data['validLayouts'])) {
            return;
        }
        foreach ($data['validLayouts'] as $key=>$layout) {
            if (($layout['name'] === 'product' && $level == 0) || ($layout['name'] === 'variant' && $level == 1)) {
                $data['currentLayoutId'] = $layout['id'];
                $customLayout = CustomLayout::getById($layout['id']);
                $data['layout'] = $customLayout->getLayoutDefinitions();
                Service::enrichLayoutDefinition($data['layout'], $object);
            } else {
                unset($data['validLayouts'][$key]);
            }
        }
        $event->setArgument('data', $data);
    }

    public function onPreSendData(GenericEvent $event): void
    {
        $object = $event->getArgument('object');
        if ($object instanceof Product) {
            $this->doModifyCustomLayouts($object, $event);
        }
    }

    public function onResolveElementAdminStyle(ElementAdminStyleEvent $event): void
    {
        $object = $event->getElement();
        if (
            $object instanceof Product || 
            $object instanceof VariantProduct
        ) {
            $event->setAdminStyle(new ProductAdminStyle($object));
        }
    }

}
