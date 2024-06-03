<?php

namespace Symfony\Config\Pimcore\Assets;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Metadata'.\DIRECTORY_SEPARATOR.'PredefinedConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Metadata'.\DIRECTORY_SEPARATOR.'ClassDefinitionsConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class MetadataConfig 
{
    private $predefined;
    private $classDefinitions;
    private $_usedProperties = [];

    /**
     * @default {"definitions":[]}
    */
    public function predefined(array $value = []): \Symfony\Config\Pimcore\Assets\Metadata\PredefinedConfig
    {
        if (null === $this->predefined) {
            $this->_usedProperties['predefined'] = true;
            $this->predefined = new \Symfony\Config\Pimcore\Assets\Metadata\PredefinedConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "predefined()" has already been initialized. You cannot pass values the second time you call predefined().');
        }

        return $this->predefined;
    }

    public function classDefinitions(array $value = []): \Symfony\Config\Pimcore\Assets\Metadata\ClassDefinitionsConfig
    {
        if (null === $this->classDefinitions) {
            $this->_usedProperties['classDefinitions'] = true;
            $this->classDefinitions = new \Symfony\Config\Pimcore\Assets\Metadata\ClassDefinitionsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "classDefinitions()" has already been initialized. You cannot pass values the second time you call classDefinitions().');
        }

        return $this->classDefinitions;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('predefined', $value)) {
            $this->_usedProperties['predefined'] = true;
            $this->predefined = new \Symfony\Config\Pimcore\Assets\Metadata\PredefinedConfig($value['predefined']);
            unset($value['predefined']);
        }

        if (array_key_exists('class_definitions', $value)) {
            $this->_usedProperties['classDefinitions'] = true;
            $this->classDefinitions = new \Symfony\Config\Pimcore\Assets\Metadata\ClassDefinitionsConfig($value['class_definitions']);
            unset($value['class_definitions']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['predefined'])) {
            $output['predefined'] = $this->predefined->toArray();
        }
        if (isset($this->_usedProperties['classDefinitions'])) {
            $output['class_definitions'] = $this->classDefinitions->toArray();
        }

        return $output;
    }

}
