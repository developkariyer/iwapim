<?php

namespace Symfony\Config\Pimcore\CustomViews;

require_once __DIR__.\DIRECTORY_SEPARATOR.'DefinitionsConfig'.\DIRECTORY_SEPARATOR.'JoinsConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DefinitionsConfig 
{
    private $id;
    private $treetype;
    private $name;
    private $condition;
    private $icon;
    private $rootfolder;
    private $showroot;
    private $classes;
    private $position;
    private $sort;
    private $expanded;
    private $having;
    private $where;
    private $treeContextMenu;
    private $joins;
    private $_usedProperties = [];

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
     * @return $this
     */
    public function treetype($value): static
    {
        $this->_usedProperties['treetype'] = true;
        $this->treetype = $value;

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
     * @return $this
     */
    public function rootfolder($value): static
    {
        $this->_usedProperties['rootfolder'] = true;
        $this->rootfolder = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function showroot($value): static
    {
        $this->_usedProperties['showroot'] = true;
        $this->showroot = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     *
     * @return $this
     */
    public function classes(mixed $value): static
    {
        $this->_usedProperties['classes'] = true;
        $this->classes = $value;

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
    public function sort($value): static
    {
        $this->_usedProperties['sort'] = true;
        $this->sort = $value;

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
    public function having($value): static
    {
        $this->_usedProperties['having'] = true;
        $this->having = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function where($value): static
    {
        $this->_usedProperties['where'] = true;
        $this->where = $value;

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

    public function joins(array $value = []): \Symfony\Config\Pimcore\CustomViews\DefinitionsConfig\JoinsConfig
    {
        $this->_usedProperties['joins'] = true;

        return $this->joins[] = new \Symfony\Config\Pimcore\CustomViews\DefinitionsConfig\JoinsConfig($value);
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('id', $value)) {
            $this->_usedProperties['id'] = true;
            $this->id = $value['id'];
            unset($value['id']);
        }

        if (array_key_exists('treetype', $value)) {
            $this->_usedProperties['treetype'] = true;
            $this->treetype = $value['treetype'];
            unset($value['treetype']);
        }

        if (array_key_exists('name', $value)) {
            $this->_usedProperties['name'] = true;
            $this->name = $value['name'];
            unset($value['name']);
        }

        if (array_key_exists('condition', $value)) {
            $this->_usedProperties['condition'] = true;
            $this->condition = $value['condition'];
            unset($value['condition']);
        }

        if (array_key_exists('icon', $value)) {
            $this->_usedProperties['icon'] = true;
            $this->icon = $value['icon'];
            unset($value['icon']);
        }

        if (array_key_exists('rootfolder', $value)) {
            $this->_usedProperties['rootfolder'] = true;
            $this->rootfolder = $value['rootfolder'];
            unset($value['rootfolder']);
        }

        if (array_key_exists('showroot', $value)) {
            $this->_usedProperties['showroot'] = true;
            $this->showroot = $value['showroot'];
            unset($value['showroot']);
        }

        if (array_key_exists('classes', $value)) {
            $this->_usedProperties['classes'] = true;
            $this->classes = $value['classes'];
            unset($value['classes']);
        }

        if (array_key_exists('position', $value)) {
            $this->_usedProperties['position'] = true;
            $this->position = $value['position'];
            unset($value['position']);
        }

        if (array_key_exists('sort', $value)) {
            $this->_usedProperties['sort'] = true;
            $this->sort = $value['sort'];
            unset($value['sort']);
        }

        if (array_key_exists('expanded', $value)) {
            $this->_usedProperties['expanded'] = true;
            $this->expanded = $value['expanded'];
            unset($value['expanded']);
        }

        if (array_key_exists('having', $value)) {
            $this->_usedProperties['having'] = true;
            $this->having = $value['having'];
            unset($value['having']);
        }

        if (array_key_exists('where', $value)) {
            $this->_usedProperties['where'] = true;
            $this->where = $value['where'];
            unset($value['where']);
        }

        if (array_key_exists('treeContextMenu', $value)) {
            $this->_usedProperties['treeContextMenu'] = true;
            $this->treeContextMenu = $value['treeContextMenu'];
            unset($value['treeContextMenu']);
        }

        if (array_key_exists('joins', $value)) {
            $this->_usedProperties['joins'] = true;
            $this->joins = array_map(fn ($v) => new \Symfony\Config\Pimcore\CustomViews\DefinitionsConfig\JoinsConfig($v), $value['joins']);
            unset($value['joins']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['id'])) {
            $output['id'] = $this->id;
        }
        if (isset($this->_usedProperties['treetype'])) {
            $output['treetype'] = $this->treetype;
        }
        if (isset($this->_usedProperties['name'])) {
            $output['name'] = $this->name;
        }
        if (isset($this->_usedProperties['condition'])) {
            $output['condition'] = $this->condition;
        }
        if (isset($this->_usedProperties['icon'])) {
            $output['icon'] = $this->icon;
        }
        if (isset($this->_usedProperties['rootfolder'])) {
            $output['rootfolder'] = $this->rootfolder;
        }
        if (isset($this->_usedProperties['showroot'])) {
            $output['showroot'] = $this->showroot;
        }
        if (isset($this->_usedProperties['classes'])) {
            $output['classes'] = $this->classes;
        }
        if (isset($this->_usedProperties['position'])) {
            $output['position'] = $this->position;
        }
        if (isset($this->_usedProperties['sort'])) {
            $output['sort'] = $this->sort;
        }
        if (isset($this->_usedProperties['expanded'])) {
            $output['expanded'] = $this->expanded;
        }
        if (isset($this->_usedProperties['having'])) {
            $output['having'] = $this->having;
        }
        if (isset($this->_usedProperties['where'])) {
            $output['where'] = $this->where;
        }
        if (isset($this->_usedProperties['treeContextMenu'])) {
            $output['treeContextMenu'] = $this->treeContextMenu;
        }
        if (isset($this->_usedProperties['joins'])) {
            $output['joins'] = array_map(fn ($v) => $v->toArray(), $this->joins);
        }

        return $output;
    }

}
