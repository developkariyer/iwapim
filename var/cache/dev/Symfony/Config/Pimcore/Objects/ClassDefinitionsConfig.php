<?php

namespace Symfony\Config\Pimcore\Objects;

require_once __DIR__.\DIRECTORY_SEPARATOR.'ClassDefinitions'.\DIRECTORY_SEPARATOR.'DataConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'ClassDefinitions'.\DIRECTORY_SEPARATOR.'LayoutConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ClassDefinitionsConfig 
{
    private $data;
    private $layout;
    private $_usedProperties = [];

    /**
     * @default {"map":[],"prefixes":[]}
    */
    public function data(array $value = []): \Symfony\Config\Pimcore\Objects\ClassDefinitions\DataConfig
    {
        if (null === $this->data) {
            $this->_usedProperties['data'] = true;
            $this->data = new \Symfony\Config\Pimcore\Objects\ClassDefinitions\DataConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "data()" has already been initialized. You cannot pass values the second time you call data().');
        }

        return $this->data;
    }

    /**
     * @default {"map":[],"prefixes":[]}
    */
    public function layout(array $value = []): \Symfony\Config\Pimcore\Objects\ClassDefinitions\LayoutConfig
    {
        if (null === $this->layout) {
            $this->_usedProperties['layout'] = true;
            $this->layout = new \Symfony\Config\Pimcore\Objects\ClassDefinitions\LayoutConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "layout()" has already been initialized. You cannot pass values the second time you call layout().');
        }

        return $this->layout;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('data', $value)) {
            $this->_usedProperties['data'] = true;
            $this->data = new \Symfony\Config\Pimcore\Objects\ClassDefinitions\DataConfig($value['data']);
            unset($value['data']);
        }

        if (array_key_exists('layout', $value)) {
            $this->_usedProperties['layout'] = true;
            $this->layout = new \Symfony\Config\Pimcore\Objects\ClassDefinitions\LayoutConfig($value['layout']);
            unset($value['layout']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['data'])) {
            $output['data'] = $this->data->toArray();
        }
        if (isset($this->_usedProperties['layout'])) {
            $output['layout'] = $this->layout->toArray();
        }

        return $output;
    }

}
