<?php

namespace Symfony\Config\Pimcore\WorkflowsConfig\PlacesConfig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class PermissionsConfig 
{
    private $condition;
    private $save;
    private $publish;
    private $unpublish;
    private $delete;
    private $rename;
    private $view;
    private $settings;
    private $versions;
    private $properties;
    private $modify;
    private $objectLayout;
    private $_usedProperties = [];

    /**
     * A symfony expression can be configured here. The first set of permissions which are matching the condition will be used.
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function condition($value): static
    {
        $this->_usedProperties['condition'] = true;
        $this->condition = $value;

        return $this;
    }

    /**
     * save permission as it can be configured in Pimcore workplaces
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function save($value): static
    {
        $this->_usedProperties['save'] = true;
        $this->save = $value;

        return $this;
    }

    /**
     * publish permission as it can be configured in Pimcore workplaces
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function publish($value): static
    {
        $this->_usedProperties['publish'] = true;
        $this->publish = $value;

        return $this;
    }

    /**
     * unpublish permission as it can be configured in Pimcore workplaces
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function unpublish($value): static
    {
        $this->_usedProperties['unpublish'] = true;
        $this->unpublish = $value;

        return $this;
    }

    /**
     * delete permission as it can be configured in Pimcore workplaces
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function delete($value): static
    {
        $this->_usedProperties['delete'] = true;
        $this->delete = $value;

        return $this;
    }

    /**
     * rename permission as it can be configured in Pimcore workplaces
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function rename($value): static
    {
        $this->_usedProperties['rename'] = true;
        $this->rename = $value;

        return $this;
    }

    /**
     * view permission as it can be configured in Pimcore workplaces
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function view($value): static
    {
        $this->_usedProperties['view'] = true;
        $this->view = $value;

        return $this;
    }

    /**
     * settings permission as it can be configured in Pimcore workplaces
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function settings($value): static
    {
        $this->_usedProperties['settings'] = true;
        $this->settings = $value;

        return $this;
    }

    /**
     * versions permission as it can be configured in Pimcore workplaces
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function versions($value): static
    {
        $this->_usedProperties['versions'] = true;
        $this->versions = $value;

        return $this;
    }

    /**
     * properties permission as it can be configured in Pimcore workplaces
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function properties($value): static
    {
        $this->_usedProperties['properties'] = true;
        $this->properties = $value;

        return $this;
    }

    /**
     * a short hand for save, publish, unpublish, delete + rename
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function modify($value): static
    {
        $this->_usedProperties['modify'] = true;
        $this->modify = $value;

        return $this;
    }

    /**
     * if set, the user will see the configured custom data object layout
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function objectLayout($value): static
    {
        $this->_usedProperties['objectLayout'] = true;
        $this->objectLayout = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('condition', $value)) {
            $this->_usedProperties['condition'] = true;
            $this->condition = $value['condition'];
            unset($value['condition']);
        }

        if (array_key_exists('save', $value)) {
            $this->_usedProperties['save'] = true;
            $this->save = $value['save'];
            unset($value['save']);
        }

        if (array_key_exists('publish', $value)) {
            $this->_usedProperties['publish'] = true;
            $this->publish = $value['publish'];
            unset($value['publish']);
        }

        if (array_key_exists('unpublish', $value)) {
            $this->_usedProperties['unpublish'] = true;
            $this->unpublish = $value['unpublish'];
            unset($value['unpublish']);
        }

        if (array_key_exists('delete', $value)) {
            $this->_usedProperties['delete'] = true;
            $this->delete = $value['delete'];
            unset($value['delete']);
        }

        if (array_key_exists('rename', $value)) {
            $this->_usedProperties['rename'] = true;
            $this->rename = $value['rename'];
            unset($value['rename']);
        }

        if (array_key_exists('view', $value)) {
            $this->_usedProperties['view'] = true;
            $this->view = $value['view'];
            unset($value['view']);
        }

        if (array_key_exists('settings', $value)) {
            $this->_usedProperties['settings'] = true;
            $this->settings = $value['settings'];
            unset($value['settings']);
        }

        if (array_key_exists('versions', $value)) {
            $this->_usedProperties['versions'] = true;
            $this->versions = $value['versions'];
            unset($value['versions']);
        }

        if (array_key_exists('properties', $value)) {
            $this->_usedProperties['properties'] = true;
            $this->properties = $value['properties'];
            unset($value['properties']);
        }

        if (array_key_exists('modify', $value)) {
            $this->_usedProperties['modify'] = true;
            $this->modify = $value['modify'];
            unset($value['modify']);
        }

        if (array_key_exists('objectLayout', $value)) {
            $this->_usedProperties['objectLayout'] = true;
            $this->objectLayout = $value['objectLayout'];
            unset($value['objectLayout']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['condition'])) {
            $output['condition'] = $this->condition;
        }
        if (isset($this->_usedProperties['save'])) {
            $output['save'] = $this->save;
        }
        if (isset($this->_usedProperties['publish'])) {
            $output['publish'] = $this->publish;
        }
        if (isset($this->_usedProperties['unpublish'])) {
            $output['unpublish'] = $this->unpublish;
        }
        if (isset($this->_usedProperties['delete'])) {
            $output['delete'] = $this->delete;
        }
        if (isset($this->_usedProperties['rename'])) {
            $output['rename'] = $this->rename;
        }
        if (isset($this->_usedProperties['view'])) {
            $output['view'] = $this->view;
        }
        if (isset($this->_usedProperties['settings'])) {
            $output['settings'] = $this->settings;
        }
        if (isset($this->_usedProperties['versions'])) {
            $output['versions'] = $this->versions;
        }
        if (isset($this->_usedProperties['properties'])) {
            $output['properties'] = $this->properties;
        }
        if (isset($this->_usedProperties['modify'])) {
            $output['modify'] = $this->modify;
        }
        if (isset($this->_usedProperties['objectLayout'])) {
            $output['objectLayout'] = $this->objectLayout;
        }

        return $output;
    }

}
