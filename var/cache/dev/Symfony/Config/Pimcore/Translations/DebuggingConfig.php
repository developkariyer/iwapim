<?php

namespace Symfony\Config\Pimcore\Translations;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DebuggingConfig 
{
    private $enabled;
    private $parameter;
    private $_usedProperties = [];

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enabled($value): static
    {
        $this->_usedProperties['enabled'] = true;
        $this->enabled = $value;

        return $this;
    }

    /**
     * @default 'pimcore_debug_translations'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function parameter($value): static
    {
        $this->_usedProperties['parameter'] = true;
        $this->parameter = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('parameter', $value)) {
            $this->_usedProperties['parameter'] = true;
            $this->parameter = $value['parameter'];
            unset($value['parameter']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['enabled'])) {
            $output['enabled'] = $this->enabled;
        }
        if (isset($this->_usedProperties['parameter'])) {
            $output['parameter'] = $this->parameter;
        }

        return $output;
    }

}
