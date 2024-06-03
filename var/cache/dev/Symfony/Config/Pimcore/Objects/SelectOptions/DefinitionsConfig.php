<?php

namespace Symfony\Config\Pimcore\Objects\SelectOptions;

require_once __DIR__.\DIRECTORY_SEPARATOR.'DefinitionsConfig'.\DIRECTORY_SEPARATOR.'SelectOptionsConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DefinitionsConfig 
{
    private $id;
    private $group;
    private $useTraits;
    private $implementsInterfaces;
    private $selectOptions;
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
    public function group($value): static
    {
        $this->_usedProperties['group'] = true;
        $this->group = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function useTraits($value): static
    {
        $this->_usedProperties['useTraits'] = true;
        $this->useTraits = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function implementsInterfaces($value): static
    {
        $this->_usedProperties['implementsInterfaces'] = true;
        $this->implementsInterfaces = $value;

        return $this;
    }

    public function selectOptions(array $value = []): \Symfony\Config\Pimcore\Objects\SelectOptions\DefinitionsConfig\SelectOptionsConfig
    {
        $this->_usedProperties['selectOptions'] = true;

        return $this->selectOptions[] = new \Symfony\Config\Pimcore\Objects\SelectOptions\DefinitionsConfig\SelectOptionsConfig($value);
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('id', $value)) {
            $this->_usedProperties['id'] = true;
            $this->id = $value['id'];
            unset($value['id']);
        }

        if (array_key_exists('group', $value)) {
            $this->_usedProperties['group'] = true;
            $this->group = $value['group'];
            unset($value['group']);
        }

        if (array_key_exists('useTraits', $value)) {
            $this->_usedProperties['useTraits'] = true;
            $this->useTraits = $value['useTraits'];
            unset($value['useTraits']);
        }

        if (array_key_exists('implementsInterfaces', $value)) {
            $this->_usedProperties['implementsInterfaces'] = true;
            $this->implementsInterfaces = $value['implementsInterfaces'];
            unset($value['implementsInterfaces']);
        }

        if (array_key_exists('selectOptions', $value)) {
            $this->_usedProperties['selectOptions'] = true;
            $this->selectOptions = array_map(fn ($v) => new \Symfony\Config\Pimcore\Objects\SelectOptions\DefinitionsConfig\SelectOptionsConfig($v), $value['selectOptions']);
            unset($value['selectOptions']);
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
        if (isset($this->_usedProperties['group'])) {
            $output['group'] = $this->group;
        }
        if (isset($this->_usedProperties['useTraits'])) {
            $output['useTraits'] = $this->useTraits;
        }
        if (isset($this->_usedProperties['implementsInterfaces'])) {
            $output['implementsInterfaces'] = $this->implementsInterfaces;
        }
        if (isset($this->_usedProperties['selectOptions'])) {
            $output['selectOptions'] = array_map(fn ($v) => $v->toArray(), $this->selectOptions);
        }

        return $output;
    }

}
