<?php

namespace Symfony\Config\CmfRouting\Dynamic\Persistence;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class OrmConfig 
{
    private $enabled;
    private $managerName;
    private $routeClass;
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
     * @default 'Symfony\\Cmf\\Bundle\\RoutingBundle\\Doctrine\\Orm\\Route'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function routeClass($value): static
    {
        $this->_usedProperties['routeClass'] = true;
        $this->routeClass = $value;

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

        if (array_key_exists('route_class', $value)) {
            $this->_usedProperties['routeClass'] = true;
            $this->routeClass = $value['route_class'];
            unset($value['route_class']);
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
        if (isset($this->_usedProperties['routeClass'])) {
            $output['route_class'] = $this->routeClass;
        }

        return $output;
    }

}
