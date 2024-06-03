<?php

namespace Symfony\Config\Pimcore\Documents;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ErrorPagesConfig 
{
    private $default;
    private $localized;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function default($value): static
    {
        $this->_usedProperties['default'] = true;
        $this->default = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function localized(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['localized'] = true;
        $this->localized = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('default', $value)) {
            $this->_usedProperties['default'] = true;
            $this->default = $value['default'];
            unset($value['default']);
        }

        if (array_key_exists('localized', $value)) {
            $this->_usedProperties['localized'] = true;
            $this->localized = $value['localized'];
            unset($value['localized']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['default'])) {
            $output['default'] = $this->default;
        }
        if (isset($this->_usedProperties['localized'])) {
            $output['localized'] = $this->localized;
        }

        return $output;
    }

}
