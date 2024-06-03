<?php

namespace Symfony\Config\PimcoreAdmin\User;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DefaultKeyBindingsConfig 
{
    private $key;
    private $action;
    private $alt;
    private $ctrl;
    private $shift;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function key($value): static
    {
        $this->_usedProperties['key'] = true;
        $this->key = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function action($value): static
    {
        $this->_usedProperties['action'] = true;
        $this->action = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function alt($value): static
    {
        $this->_usedProperties['alt'] = true;
        $this->alt = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function ctrl($value): static
    {
        $this->_usedProperties['ctrl'] = true;
        $this->ctrl = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function shift($value): static
    {
        $this->_usedProperties['shift'] = true;
        $this->shift = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('key', $value)) {
            $this->_usedProperties['key'] = true;
            $this->key = $value['key'];
            unset($value['key']);
        }

        if (array_key_exists('action', $value)) {
            $this->_usedProperties['action'] = true;
            $this->action = $value['action'];
            unset($value['action']);
        }

        if (array_key_exists('alt', $value)) {
            $this->_usedProperties['alt'] = true;
            $this->alt = $value['alt'];
            unset($value['alt']);
        }

        if (array_key_exists('ctrl', $value)) {
            $this->_usedProperties['ctrl'] = true;
            $this->ctrl = $value['ctrl'];
            unset($value['ctrl']);
        }

        if (array_key_exists('shift', $value)) {
            $this->_usedProperties['shift'] = true;
            $this->shift = $value['shift'];
            unset($value['shift']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['key'])) {
            $output['key'] = $this->key;
        }
        if (isset($this->_usedProperties['action'])) {
            $output['action'] = $this->action;
        }
        if (isset($this->_usedProperties['alt'])) {
            $output['alt'] = $this->alt;
        }
        if (isset($this->_usedProperties['ctrl'])) {
            $output['ctrl'] = $this->ctrl;
        }
        if (isset($this->_usedProperties['shift'])) {
            $output['shift'] = $this->shift;
        }

        return $output;
    }

}
