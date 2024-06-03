<?php

namespace Symfony\Config\Pimcore;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Routing'.\DIRECTORY_SEPARATOR.'StaticConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class RoutingConfig 
{
    private $static;
    private $_usedProperties = [];

    /**
     * @default {"locale_params":[]}
    */
    public function static(array $value = []): \Symfony\Config\Pimcore\Routing\StaticConfig
    {
        if (null === $this->static) {
            $this->_usedProperties['static'] = true;
            $this->static = new \Symfony\Config\Pimcore\Routing\StaticConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "static()" has already been initialized. You cannot pass values the second time you call static().');
        }

        return $this->static;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('static', $value)) {
            $this->_usedProperties['static'] = true;
            $this->static = new \Symfony\Config\Pimcore\Routing\StaticConfig($value['static']);
            unset($value['static']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['static'])) {
            $output['static'] = $this->static->toArray();
        }

        return $output;
    }

}
