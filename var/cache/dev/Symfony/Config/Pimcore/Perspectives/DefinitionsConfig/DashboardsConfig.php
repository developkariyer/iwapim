<?php

namespace Symfony\Config\Pimcore\Perspectives\DefinitionsConfig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DashboardsConfig 
{
    private $disabledPortlets;
    private $predefined;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     *
     * @return $this
     */
    public function disabledPortlets(mixed $value): static
    {
        $this->_usedProperties['disabledPortlets'] = true;
        $this->disabledPortlets = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     *
     * @return $this
     */
    public function predefined(mixed $value): static
    {
        $this->_usedProperties['predefined'] = true;
        $this->predefined = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('disabledPortlets', $value)) {
            $this->_usedProperties['disabledPortlets'] = true;
            $this->disabledPortlets = $value['disabledPortlets'];
            unset($value['disabledPortlets']);
        }

        if (array_key_exists('predefined', $value)) {
            $this->_usedProperties['predefined'] = true;
            $this->predefined = $value['predefined'];
            unset($value['predefined']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['disabledPortlets'])) {
            $output['disabledPortlets'] = $this->disabledPortlets;
        }
        if (isset($this->_usedProperties['predefined'])) {
            $output['predefined'] = $this->predefined;
        }

        return $output;
    }

}
