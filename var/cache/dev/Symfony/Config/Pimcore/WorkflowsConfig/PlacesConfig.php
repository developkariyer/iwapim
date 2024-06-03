<?php

namespace Symfony\Config\Pimcore\WorkflowsConfig;

require_once __DIR__.\DIRECTORY_SEPARATOR.'PlacesConfig'.\DIRECTORY_SEPARATOR.'PermissionsConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class PlacesConfig 
{
    private $label;
    private $title;
    private $color;
    private $colorInverted;
    private $visibleInHeader;
    private $permissions;
    private $_usedProperties = [];

    /**
     * Nice name which will be used in the Pimcore backend.
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function label($value): static
    {
        $this->_usedProperties['label'] = true;
        $this->label = $value;

        return $this;
    }

    /**
     * Title/tooltip for this place when it is displayed in the header of the Pimcore element detail view in the backend.
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function title($value): static
    {
        $this->_usedProperties['title'] = true;
        $this->title = $value;

        return $this;
    }

    /**
     * Color of the place which will be used in the Pimcore backend.
     * @default '#bfdadc'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function color($value): static
    {
        $this->_usedProperties['color'] = true;
        $this->color = $value;

        return $this;
    }

    /**
     * If set to true the color will be used as border and font color otherwise as background color.
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function colorInverted($value): static
    {
        $this->_usedProperties['colorInverted'] = true;
        $this->colorInverted = $value;

        return $this;
    }

    /**
     * If set to false, the place will be hidden in the header of the Pimcore element detail view in the backend.
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function visibleInHeader($value): static
    {
        $this->_usedProperties['visibleInHeader'] = true;
        $this->visibleInHeader = $value;

        return $this;
    }

    public function permissions(array $value = []): \Symfony\Config\Pimcore\WorkflowsConfig\PlacesConfig\PermissionsConfig
    {
        $this->_usedProperties['permissions'] = true;

        return $this->permissions[] = new \Symfony\Config\Pimcore\WorkflowsConfig\PlacesConfig\PermissionsConfig($value);
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('label', $value)) {
            $this->_usedProperties['label'] = true;
            $this->label = $value['label'];
            unset($value['label']);
        }

        if (array_key_exists('title', $value)) {
            $this->_usedProperties['title'] = true;
            $this->title = $value['title'];
            unset($value['title']);
        }

        if (array_key_exists('color', $value)) {
            $this->_usedProperties['color'] = true;
            $this->color = $value['color'];
            unset($value['color']);
        }

        if (array_key_exists('colorInverted', $value)) {
            $this->_usedProperties['colorInverted'] = true;
            $this->colorInverted = $value['colorInverted'];
            unset($value['colorInverted']);
        }

        if (array_key_exists('visibleInHeader', $value)) {
            $this->_usedProperties['visibleInHeader'] = true;
            $this->visibleInHeader = $value['visibleInHeader'];
            unset($value['visibleInHeader']);
        }

        if (array_key_exists('permissions', $value)) {
            $this->_usedProperties['permissions'] = true;
            $this->permissions = array_map(fn ($v) => new \Symfony\Config\Pimcore\WorkflowsConfig\PlacesConfig\PermissionsConfig($v), $value['permissions']);
            unset($value['permissions']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['label'])) {
            $output['label'] = $this->label;
        }
        if (isset($this->_usedProperties['title'])) {
            $output['title'] = $this->title;
        }
        if (isset($this->_usedProperties['color'])) {
            $output['color'] = $this->color;
        }
        if (isset($this->_usedProperties['colorInverted'])) {
            $output['colorInverted'] = $this->colorInverted;
        }
        if (isset($this->_usedProperties['visibleInHeader'])) {
            $output['visibleInHeader'] = $this->visibleInHeader;
        }
        if (isset($this->_usedProperties['permissions'])) {
            $output['permissions'] = array_map(fn ($v) => $v->toArray(), $this->permissions);
        }

        return $output;
    }

}
