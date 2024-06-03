<?php

namespace Symfony\Config\PimcoreAdmin;

require_once __DIR__.\DIRECTORY_SEPARATOR.'ConfigLocation'.\DIRECTORY_SEPARATOR.'AdminSystemSettingsConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ConfigLocationConfig 
{
    private $adminSystemSettings;
    private $_usedProperties = [];

    /**
     * @default {"write_target":{"type":"symfony-config","options":{"directory":"\/var\/www\/iwapim\/var\/config\/admin_system_settings"}},"read_target":{"type":null,"options":{"directory":null}}}
    */
    public function adminSystemSettings(array $value = []): \Symfony\Config\PimcoreAdmin\ConfigLocation\AdminSystemSettingsConfig
    {
        if (null === $this->adminSystemSettings) {
            $this->_usedProperties['adminSystemSettings'] = true;
            $this->adminSystemSettings = new \Symfony\Config\PimcoreAdmin\ConfigLocation\AdminSystemSettingsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "adminSystemSettings()" has already been initialized. You cannot pass values the second time you call adminSystemSettings().');
        }

        return $this->adminSystemSettings;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('admin_system_settings', $value)) {
            $this->_usedProperties['adminSystemSettings'] = true;
            $this->adminSystemSettings = new \Symfony\Config\PimcoreAdmin\ConfigLocation\AdminSystemSettingsConfig($value['admin_system_settings']);
            unset($value['admin_system_settings']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['adminSystemSettings'])) {
            $output['admin_system_settings'] = $this->adminSystemSettings->toArray();
        }

        return $output;
    }

}
