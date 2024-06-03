<?php

namespace Symfony\Config\Pimcore\ConfigLocation;

require_once __DIR__.\DIRECTORY_SEPARATOR.'PredefinedProperties'.\DIRECTORY_SEPARATOR.'WriteTargetConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class PredefinedPropertiesConfig 
{
    private $writeTarget;
    private $_usedProperties = [];

    /**
     * @default {"type":"symfony-config","options":{"directory":"\/var\/www\/iwapim\/var\/config\/predefined_properties"}}
    */
    public function writeTarget(array $value = []): \Symfony\Config\Pimcore\ConfigLocation\PredefinedProperties\WriteTargetConfig
    {
        if (null === $this->writeTarget) {
            $this->_usedProperties['writeTarget'] = true;
            $this->writeTarget = new \Symfony\Config\Pimcore\ConfigLocation\PredefinedProperties\WriteTargetConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "writeTarget()" has already been initialized. You cannot pass values the second time you call writeTarget().');
        }

        return $this->writeTarget;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('write_target', $value)) {
            $this->_usedProperties['writeTarget'] = true;
            $this->writeTarget = new \Symfony\Config\Pimcore\ConfigLocation\PredefinedProperties\WriteTargetConfig($value['write_target']);
            unset($value['write_target']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['writeTarget'])) {
            $output['write_target'] = $this->writeTarget->toArray();
        }

        return $output;
    }

}
