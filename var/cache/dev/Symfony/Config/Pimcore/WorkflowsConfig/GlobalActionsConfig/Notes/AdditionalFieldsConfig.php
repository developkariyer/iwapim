<?php

namespace Symfony\Config\Pimcore\WorkflowsConfig\GlobalActionsConfig\Notes;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class AdditionalFieldsConfig 
{
    private $name;
    private $fieldType;
    private $title;
    private $required;
    private $setterFn;
    private $fieldTypeSettings;
    private $_usedProperties = [];

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
     * @param ParamConfigurator|'input'|'textarea'|'select'|'datetime'|'date'|'user'|'checkbox' $value
     * @return $this
     */
    public function fieldType($value): static
    {
        $this->_usedProperties['fieldType'] = true;
        $this->fieldType = $value;

        return $this;
    }

    /**
     * @default null
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
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function required($value): static
    {
        $this->_usedProperties['required'] = true;
        $this->required = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function setterFn($value): static
    {
        $this->_usedProperties['setterFn'] = true;
        $this->setterFn = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function fieldTypeSettings(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['fieldTypeSettings'] = true;
        $this->fieldTypeSettings = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('name', $value)) {
            $this->_usedProperties['name'] = true;
            $this->name = $value['name'];
            unset($value['name']);
        }

        if (array_key_exists('fieldType', $value)) {
            $this->_usedProperties['fieldType'] = true;
            $this->fieldType = $value['fieldType'];
            unset($value['fieldType']);
        }

        if (array_key_exists('title', $value)) {
            $this->_usedProperties['title'] = true;
            $this->title = $value['title'];
            unset($value['title']);
        }

        if (array_key_exists('required', $value)) {
            $this->_usedProperties['required'] = true;
            $this->required = $value['required'];
            unset($value['required']);
        }

        if (array_key_exists('setterFn', $value)) {
            $this->_usedProperties['setterFn'] = true;
            $this->setterFn = $value['setterFn'];
            unset($value['setterFn']);
        }

        if (array_key_exists('fieldTypeSettings', $value)) {
            $this->_usedProperties['fieldTypeSettings'] = true;
            $this->fieldTypeSettings = $value['fieldTypeSettings'];
            unset($value['fieldTypeSettings']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['name'])) {
            $output['name'] = $this->name;
        }
        if (isset($this->_usedProperties['fieldType'])) {
            $output['fieldType'] = $this->fieldType;
        }
        if (isset($this->_usedProperties['title'])) {
            $output['title'] = $this->title;
        }
        if (isset($this->_usedProperties['required'])) {
            $output['required'] = $this->required;
        }
        if (isset($this->_usedProperties['setterFn'])) {
            $output['setterFn'] = $this->setterFn;
        }
        if (isset($this->_usedProperties['fieldTypeSettings'])) {
            $output['fieldTypeSettings'] = $this->fieldTypeSettings;
        }

        return $output;
    }

}
