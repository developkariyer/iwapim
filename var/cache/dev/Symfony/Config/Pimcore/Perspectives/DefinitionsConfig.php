<?php

namespace Symfony\Config\Pimcore\Perspectives;

require_once __DIR__.\DIRECTORY_SEPARATOR.'DefinitionsConfig'.\DIRECTORY_SEPARATOR.'DashboardsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'DefinitionsConfig'.\DIRECTORY_SEPARATOR.'ElementTreeConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DefinitionsConfig 
{
    private $iconCls;
    private $icon;
    private $toolbar;
    private $dashboards;
    private $elementTree;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function iconCls($value): static
    {
        $this->_usedProperties['iconCls'] = true;
        $this->iconCls = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function icon($value): static
    {
        $this->_usedProperties['icon'] = true;
        $this->icon = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     *
     * @return $this
     */
    public function toolbar(mixed $value): static
    {
        $this->_usedProperties['toolbar'] = true;
        $this->toolbar = $value;

        return $this;
    }

    public function dashboards(array $value = []): \Symfony\Config\Pimcore\Perspectives\DefinitionsConfig\DashboardsConfig
    {
        if (null === $this->dashboards) {
            $this->_usedProperties['dashboards'] = true;
            $this->dashboards = new \Symfony\Config\Pimcore\Perspectives\DefinitionsConfig\DashboardsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "dashboards()" has already been initialized. You cannot pass values the second time you call dashboards().');
        }

        return $this->dashboards;
    }

    public function elementTree(array $value = []): \Symfony\Config\Pimcore\Perspectives\DefinitionsConfig\ElementTreeConfig
    {
        $this->_usedProperties['elementTree'] = true;

        return $this->elementTree[] = new \Symfony\Config\Pimcore\Perspectives\DefinitionsConfig\ElementTreeConfig($value);
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('iconCls', $value)) {
            $this->_usedProperties['iconCls'] = true;
            $this->iconCls = $value['iconCls'];
            unset($value['iconCls']);
        }

        if (array_key_exists('icon', $value)) {
            $this->_usedProperties['icon'] = true;
            $this->icon = $value['icon'];
            unset($value['icon']);
        }

        if (array_key_exists('toolbar', $value)) {
            $this->_usedProperties['toolbar'] = true;
            $this->toolbar = $value['toolbar'];
            unset($value['toolbar']);
        }

        if (array_key_exists('dashboards', $value)) {
            $this->_usedProperties['dashboards'] = true;
            $this->dashboards = new \Symfony\Config\Pimcore\Perspectives\DefinitionsConfig\DashboardsConfig($value['dashboards']);
            unset($value['dashboards']);
        }

        if (array_key_exists('elementTree', $value)) {
            $this->_usedProperties['elementTree'] = true;
            $this->elementTree = array_map(fn ($v) => new \Symfony\Config\Pimcore\Perspectives\DefinitionsConfig\ElementTreeConfig($v), $value['elementTree']);
            unset($value['elementTree']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['iconCls'])) {
            $output['iconCls'] = $this->iconCls;
        }
        if (isset($this->_usedProperties['icon'])) {
            $output['icon'] = $this->icon;
        }
        if (isset($this->_usedProperties['toolbar'])) {
            $output['toolbar'] = $this->toolbar;
        }
        if (isset($this->_usedProperties['dashboards'])) {
            $output['dashboards'] = $this->dashboards->toArray();
        }
        if (isset($this->_usedProperties['elementTree'])) {
            $output['elementTree'] = array_map(fn ($v) => $v->toArray(), $this->elementTree);
        }

        return $output;
    }

}
