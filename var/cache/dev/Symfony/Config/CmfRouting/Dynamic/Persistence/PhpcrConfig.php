<?php

namespace Symfony\Config\CmfRouting\Dynamic\Persistence;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class PhpcrConfig 
{
    private $enabled;
    private $managerName;
    private $routeBasepaths;
    private $enableInitializer;
    private $_usedProperties = [];

    /**
     * @default false
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
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function managerName($value): static
    {
        $this->_usedProperties['managerName'] = true;
        $this->managerName = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed>|string $value
     *
     * @return $this
     */
    public function routeBasepaths(ParamConfigurator|string|array $value): static
    {
        $this->_usedProperties['routeBasepaths'] = true;
        $this->routeBasepaths = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enableInitializer($value): static
    {
        $this->_usedProperties['enableInitializer'] = true;
        $this->enableInitializer = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('manager_name', $value)) {
            $this->_usedProperties['managerName'] = true;
            $this->managerName = $value['manager_name'];
            unset($value['manager_name']);
        }

        if (array_key_exists('route_basepaths', $value)) {
            $this->_usedProperties['routeBasepaths'] = true;
            $this->routeBasepaths = $value['route_basepaths'];
            unset($value['route_basepaths']);
        }

        if (array_key_exists('enable_initializer', $value)) {
            $this->_usedProperties['enableInitializer'] = true;
            $this->enableInitializer = $value['enable_initializer'];
            unset($value['enable_initializer']);
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
        if (isset($this->_usedProperties['managerName'])) {
            $output['manager_name'] = $this->managerName;
        }
        if (isset($this->_usedProperties['routeBasepaths'])) {
            $output['route_basepaths'] = $this->routeBasepaths;
        }
        if (isset($this->_usedProperties['enableInitializer'])) {
            $output['enable_initializer'] = $this->enableInitializer;
        }

        return $output;
    }

}
