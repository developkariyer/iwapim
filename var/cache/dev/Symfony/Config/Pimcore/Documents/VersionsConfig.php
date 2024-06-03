<?php

namespace Symfony\Config\Pimcore\Documents;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class VersionsConfig 
{
    private $days;
    private $steps;
    private $disableStackTrace;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function days($value): static
    {
        $this->_usedProperties['days'] = true;
        $this->days = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function steps($value): static
    {
        $this->_usedProperties['steps'] = true;
        $this->steps = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function disableStackTrace($value): static
    {
        $this->_usedProperties['disableStackTrace'] = true;
        $this->disableStackTrace = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('days', $value)) {
            $this->_usedProperties['days'] = true;
            $this->days = $value['days'];
            unset($value['days']);
        }

        if (array_key_exists('steps', $value)) {
            $this->_usedProperties['steps'] = true;
            $this->steps = $value['steps'];
            unset($value['steps']);
        }

        if (array_key_exists('disable_stack_trace', $value)) {
            $this->_usedProperties['disableStackTrace'] = true;
            $this->disableStackTrace = $value['disable_stack_trace'];
            unset($value['disable_stack_trace']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['days'])) {
            $output['days'] = $this->days;
        }
        if (isset($this->_usedProperties['steps'])) {
            $output['steps'] = $this->steps;
        }
        if (isset($this->_usedProperties['disableStackTrace'])) {
            $output['disable_stack_trace'] = $this->disableStackTrace;
        }

        return $output;
    }

}
