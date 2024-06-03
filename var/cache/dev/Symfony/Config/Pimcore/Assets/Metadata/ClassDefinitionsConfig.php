<?php

namespace Symfony\Config\Pimcore\Assets\Metadata;

require_once __DIR__.\DIRECTORY_SEPARATOR.'ClassDefinitions'.\DIRECTORY_SEPARATOR.'DataConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ClassDefinitionsConfig 
{
    private $data;
    private $_usedProperties = [];

    public function data(array $value = []): \Symfony\Config\Pimcore\Assets\Metadata\ClassDefinitions\DataConfig
    {
        if (null === $this->data) {
            $this->_usedProperties['data'] = true;
            $this->data = new \Symfony\Config\Pimcore\Assets\Metadata\ClassDefinitions\DataConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "data()" has already been initialized. You cannot pass values the second time you call data().');
        }

        return $this->data;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('data', $value)) {
            $this->_usedProperties['data'] = true;
            $this->data = new \Symfony\Config\Pimcore\Assets\Metadata\ClassDefinitions\DataConfig($value['data']);
            unset($value['data']);
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

        return $output;
    }

}
