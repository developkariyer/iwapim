<?php

namespace Symfony\Config\Pimcore\Objects;

require_once __DIR__.\DIRECTORY_SEPARATOR.'CustomLayouts'.\DIRECTORY_SEPARATOR.'DefinitionsConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class CustomLayoutsConfig 
{
    private $definitions;
    private $_usedProperties = [];

    public function definitions(array $value = []): \Symfony\Config\Pimcore\Objects\CustomLayouts\DefinitionsConfig
    {
        $this->_usedProperties['definitions'] = true;

        return $this->definitions[] = new \Symfony\Config\Pimcore\Objects\CustomLayouts\DefinitionsConfig($value);
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('definitions', $value)) {
            $this->_usedProperties['definitions'] = true;
            $this->definitions = array_map(fn ($v) => new \Symfony\Config\Pimcore\Objects\CustomLayouts\DefinitionsConfig($v), $value['definitions']);
            unset($value['definitions']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['definitions'])) {
            $output['definitions'] = array_map(fn ($v) => $v->toArray(), $this->definitions);
        }

        return $output;
    }

}
