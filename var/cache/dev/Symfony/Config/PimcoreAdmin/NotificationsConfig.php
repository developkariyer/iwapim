<?php

namespace Symfony\Config\PimcoreAdmin;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Notifications'.\DIRECTORY_SEPARATOR.'CheckNewNotificationConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class NotificationsConfig 
{
    private $enabled;
    private $checkNewNotification;
    private $_usedProperties = [];

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enabled($value): static
    {
        $this->_usedProperties['enabled'] = true;
        $this->enabled = $value;

        return $this;
    }

    /**
     * Can be used to enable or disable the check of new notifications (url: /admin/notification/find-last-unread).
     * @default {"enabled":true,"interval":30}
    */
    public function checkNewNotification(array $value = []): \Symfony\Config\PimcoreAdmin\Notifications\CheckNewNotificationConfig
    {
        if (null === $this->checkNewNotification) {
            $this->_usedProperties['checkNewNotification'] = true;
            $this->checkNewNotification = new \Symfony\Config\PimcoreAdmin\Notifications\CheckNewNotificationConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "checkNewNotification()" has already been initialized. You cannot pass values the second time you call checkNewNotification().');
        }

        return $this->checkNewNotification;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('check_new_notification', $value)) {
            $this->_usedProperties['checkNewNotification'] = true;
            $this->checkNewNotification = new \Symfony\Config\PimcoreAdmin\Notifications\CheckNewNotificationConfig($value['check_new_notification']);
            unset($value['check_new_notification']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['enabled'])) {
            $output['enabled'] = $this->enabled;
        }
        if (isset($this->_usedProperties['checkNewNotification'])) {
            $output['check_new_notification'] = $this->checkNewNotification->toArray();
        }

        return $output;
    }

}
