<?php

namespace Symfony\Config\Pimcore\Assets\Image\Thumbnails\DefinitionsConfig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ItemsConfig 
{
    private $method;
    private $arguments;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function method($value): static
    {
        $this->_usedProperties['method'] = true;
        $this->method = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function arguments(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['arguments'] = true;
        $this->arguments = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('method', $value)) {
            $this->_usedProperties['method'] = true;
            $this->method = $value['method'];
            unset($value['method']);
        }

        if (array_key_exists('arguments', $value)) {
            $this->_usedProperties['arguments'] = true;
            $this->arguments = $value['arguments'];
            unset($value['arguments']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['method'])) {
            $output['method'] = $this->method;
        }
        if (isset($this->_usedProperties['arguments'])) {
            $output['arguments'] = $this->arguments;
        }

        return $output;
    }

}
