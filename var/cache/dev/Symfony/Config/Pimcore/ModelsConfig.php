<?php

namespace Symfony\Config\Pimcore;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ModelsConfig 
{
    private $classOverrides;
    private $_usedProperties = [];

    /**
     * @return $this
     */
    public function classOverrides(string $name, mixed $value): static
    {
        $this->_usedProperties['classOverrides'] = true;
        $this->classOverrides[$name] = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('class_overrides', $value)) {
            $this->_usedProperties['classOverrides'] = true;
            $this->classOverrides = $value['class_overrides'];
            unset($value['class_overrides']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['classOverrides'])) {
            $output['class_overrides'] = $this->classOverrides;
        }

        return $output;
    }

}
