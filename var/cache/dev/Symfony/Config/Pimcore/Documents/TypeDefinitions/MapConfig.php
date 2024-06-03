<?php

namespace Symfony\Config\Pimcore\Documents\TypeDefinitions;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class MapConfig 
{
    private $class;
    private $translatable;
    private $validTable;
    private $directRoute;
    private $translatableInheritance;
    private $childrenSupported;
    private $onlyPrintableChildrens;
    private $predefinedDocumentTypes;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function class($value): static
    {
        $this->_usedProperties['class'] = true;
        $this->class = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function translatable($value): static
    {
        $this->_usedProperties['translatable'] = true;
        $this->translatable = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function validTable($value): static
    {
        $this->_usedProperties['validTable'] = true;
        $this->validTable = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function directRoute($value): static
    {
        $this->_usedProperties['directRoute'] = true;
        $this->directRoute = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function translatableInheritance($value): static
    {
        $this->_usedProperties['translatableInheritance'] = true;
        $this->translatableInheritance = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function childrenSupported($value): static
    {
        $this->_usedProperties['childrenSupported'] = true;
        $this->childrenSupported = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function onlyPrintableChildrens($value): static
    {
        $this->_usedProperties['onlyPrintableChildrens'] = true;
        $this->onlyPrintableChildrens = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function predefinedDocumentTypes($value): static
    {
        $this->_usedProperties['predefinedDocumentTypes'] = true;
        $this->predefinedDocumentTypes = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('class', $value)) {
            $this->_usedProperties['class'] = true;
            $this->class = $value['class'];
            unset($value['class']);
        }

        if (array_key_exists('translatable', $value)) {
            $this->_usedProperties['translatable'] = true;
            $this->translatable = $value['translatable'];
            unset($value['translatable']);
        }

        if (array_key_exists('valid_table', $value)) {
            $this->_usedProperties['validTable'] = true;
            $this->validTable = $value['valid_table'];
            unset($value['valid_table']);
        }

        if (array_key_exists('direct_route', $value)) {
            $this->_usedProperties['directRoute'] = true;
            $this->directRoute = $value['direct_route'];
            unset($value['direct_route']);
        }

        if (array_key_exists('translatable_inheritance', $value)) {
            $this->_usedProperties['translatableInheritance'] = true;
            $this->translatableInheritance = $value['translatable_inheritance'];
            unset($value['translatable_inheritance']);
        }

        if (array_key_exists('children_supported', $value)) {
            $this->_usedProperties['childrenSupported'] = true;
            $this->childrenSupported = $value['children_supported'];
            unset($value['children_supported']);
        }

        if (array_key_exists('only_printable_childrens', $value)) {
            $this->_usedProperties['onlyPrintableChildrens'] = true;
            $this->onlyPrintableChildrens = $value['only_printable_childrens'];
            unset($value['only_printable_childrens']);
        }

        if (array_key_exists('predefined_document_types', $value)) {
            $this->_usedProperties['predefinedDocumentTypes'] = true;
            $this->predefinedDocumentTypes = $value['predefined_document_types'];
            unset($value['predefined_document_types']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['class'])) {
            $output['class'] = $this->class;
        }
        if (isset($this->_usedProperties['translatable'])) {
            $output['translatable'] = $this->translatable;
        }
        if (isset($this->_usedProperties['validTable'])) {
            $output['valid_table'] = $this->validTable;
        }
        if (isset($this->_usedProperties['directRoute'])) {
            $output['direct_route'] = $this->directRoute;
        }
        if (isset($this->_usedProperties['translatableInheritance'])) {
            $output['translatable_inheritance'] = $this->translatableInheritance;
        }
        if (isset($this->_usedProperties['childrenSupported'])) {
            $output['children_supported'] = $this->childrenSupported;
        }
        if (isset($this->_usedProperties['onlyPrintableChildrens'])) {
            $output['only_printable_childrens'] = $this->onlyPrintableChildrens;
        }
        if (isset($this->_usedProperties['predefinedDocumentTypes'])) {
            $output['predefined_document_types'] = $this->predefinedDocumentTypes;
        }

        return $output;
    }

}
