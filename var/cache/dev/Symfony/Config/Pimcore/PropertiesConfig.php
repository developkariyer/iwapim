<?php

namespace Symfony\Config\Pimcore;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Properties'.\DIRECTORY_SEPARATOR.'PredefinedConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class PropertiesConfig 
{
    private $predefined;
    private $_usedProperties = [];
    private $_extraKeys;

    /**
     * @default {"definitions":[]}
    */
    public function predefined(array $value = []): \Symfony\Config\Pimcore\Properties\PredefinedConfig
    {
        if (null === $this->predefined) {
            $this->_usedProperties['predefined'] = true;
            $this->predefined = new \Symfony\Config\Pimcore\Properties\PredefinedConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "predefined()" has already been initialized. You cannot pass values the second time you call predefined().');
        }

        return $this->predefined;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('predefined', $value)) {
            $this->_usedProperties['predefined'] = true;
            $this->predefined = new \Symfony\Config\Pimcore\Properties\PredefinedConfig($value['predefined']);
            unset($value['predefined']);
        }

        $this->_extraKeys = $value;

    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['predefined'])) {
            $output['predefined'] = $this->predefined->toArray();
        }

        return $output + $this->_extraKeys;
    }

    /**
     * @param ParamConfigurator|mixed $value
     *
     * @return $this
     */
    public function set(string $key, mixed $value): static
    {
        $this->_extraKeys[$key] = $value;

        return $this;
    }

}
