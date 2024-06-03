<?php

namespace Symfony\Config\Pimcore\Assets\Metadata\ClassDefinitions;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DataConfig 
{
    private $map;
    private $prefixes;
    private $_usedProperties = [];

    /**
     * @return $this
     */
    public function map(string $name, mixed $value): static
    {
        $this->_usedProperties['map'] = true;
        $this->map[$name] = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function prefixes(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['prefixes'] = true;
        $this->prefixes = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('map', $value)) {
            $this->_usedProperties['map'] = true;
            $this->map = $value['map'];
            unset($value['map']);
        }

        if (array_key_exists('prefixes', $value)) {
            $this->_usedProperties['prefixes'] = true;
            $this->prefixes = $value['prefixes'];
            unset($value['prefixes']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['map'])) {
            $output['map'] = $this->map;
        }
        if (isset($this->_usedProperties['prefixes'])) {
            $output['prefixes'] = $this->prefixes;
        }

        return $output;
    }

}
