<?php

namespace Symfony\Config\Pimcore\Perspectives\DefinitionsConfig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ElementTreeConfig 
{
    private $type;
    private $position;
    private $name;
    private $expanded;
    private $hidden;
    private $sort;
    private $id;
    private $treeContextMenu;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function type($value): static
    {
        $this->_usedProperties['type'] = true;
        $this->type = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function position($value): static
    {
        $this->_usedProperties['position'] = true;
        $this->position = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function name($value): static
    {
        $this->_usedProperties['name'] = true;
        $this->name = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function expanded($value): static
    {
        $this->_usedProperties['expanded'] = true;
        $this->expanded = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function hidden($value): static
    {
        $this->_usedProperties['hidden'] = true;
        $this->hidden = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function sort($value): static
    {
        $this->_usedProperties['sort'] = true;
        $this->sort = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function id($value): static
    {
        $this->_usedProperties['id'] = true;
        $this->id = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     *
     * @return $this
     */
    public function treeContextMenu(mixed $value): static
    {
        $this->_usedProperties['treeContextMenu'] = true;
        $this->treeContextMenu = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('type', $value)) {
            $this->_usedProperties['type'] = true;
            $this->type = $value['type'];
            unset($value['type']);
        }

        if (array_key_exists('position', $value)) {
            $this->_usedProperties['position'] = true;
            $this->position = $value['position'];
            unset($value['position']);
        }

        if (array_key_exists('name', $value)) {
            $this->_usedProperties['name'] = true;
            $this->name = $value['name'];
            unset($value['name']);
        }

        if (array_key_exists('expanded', $value)) {
            $this->_usedProperties['expanded'] = true;
            $this->expanded = $value['expanded'];
            unset($value['expanded']);
        }

        if (array_key_exists('hidden', $value)) {
            $this->_usedProperties['hidden'] = true;
            $this->hidden = $value['hidden'];
            unset($value['hidden']);
        }

        if (array_key_exists('sort', $value)) {
            $this->_usedProperties['sort'] = true;
            $this->sort = $value['sort'];
            unset($value['sort']);
        }

        if (array_key_exists('id', $value)) {
            $this->_usedProperties['id'] = true;
            $this->id = $value['id'];
            unset($value['id']);
        }

        if (array_key_exists('treeContextMenu', $value)) {
            $this->_usedProperties['treeContextMenu'] = true;
            $this->treeContextMenu = $value['treeContextMenu'];
            unset($value['treeContextMenu']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['type'])) {
            $output['type'] = $this->type;
        }
        if (isset($this->_usedProperties['position'])) {
            $output['position'] = $this->position;
        }
        if (isset($this->_usedProperties['name'])) {
            $output['name'] = $this->name;
        }
        if (isset($this->_usedProperties['expanded'])) {
            $output['expanded'] = $this->expanded;
        }
        if (isset($this->_usedProperties['hidden'])) {
            $output['hidden'] = $this->hidden;
        }
        if (isset($this->_usedProperties['sort'])) {
            $output['sort'] = $this->sort;
        }
        if (isset($this->_usedProperties['id'])) {
            $output['id'] = $this->id;
        }
        if (isset($this->_usedProperties['treeContextMenu'])) {
            $output['treeContextMenu'] = $this->treeContextMenu;
        }

        return $output;
    }

}
