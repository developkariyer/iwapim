<?php

namespace Symfony\Config\Pimcore\WorkflowsConfig\TransitionsConfig\Options;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Notes'.\DIRECTORY_SEPARATOR.'AdditionalFieldsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Notes'.\DIRECTORY_SEPARATOR.'CustomHtmlConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class NotesConfig 
{
    private $commentEnabled;
    private $commentRequired;
    private $commentSetterFn;
    private $commentGetterFn;
    private $type;
    private $title;
    private $additionalFields;
    private $customHtml;
    private $_usedProperties = [];

    /**
     * If enabled a detail window will open when the user executes the transition. In this detail view the user be asked to enter a "comment". This comment then will be used as comment for the notes/events feature.
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function commentEnabled($value): static
    {
        $this->_usedProperties['commentEnabled'] = true;
        $this->commentEnabled = $value;

        return $this;
    }

    /**
     * Set this to true if the comment should be a required field.
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function commentRequired($value): static
    {
        $this->_usedProperties['commentRequired'] = true;
        $this->commentRequired = $value;

        return $this;
    }

    /**
     * Can be used for data objects. The comment will be saved to the data object additionally to the notes/events through this setter function.
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function commentSetterFn($value): static
    {
        $this->_usedProperties['commentSetterFn'] = true;
        $this->commentSetterFn = $value;

        return $this;
    }

    /**
     * Can be used for data objects to prefill the comment field with data from the data object.
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function commentGetterFn($value): static
    {
        $this->_usedProperties['commentGetterFn'] = true;
        $this->commentGetterFn = $value;

        return $this;
    }

    /**
     * Set's the type string in the saved note.
     * @default 'Status update'
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
     * An optional alternative "title" for the note, if blank the actions transition result is used.
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
     * Add additional field to the transition detail window.
    */
    public function additionalFields(array $value = []): \Symfony\Config\Pimcore\WorkflowsConfig\TransitionsConfig\Options\Notes\AdditionalFieldsConfig
    {
        $this->_usedProperties['additionalFields'] = true;

        return $this->additionalFields[] = new \Symfony\Config\Pimcore\WorkflowsConfig\TransitionsConfig\Options\Notes\AdditionalFieldsConfig($value);
    }

    public function customHtml(array $value = []): \Symfony\Config\Pimcore\WorkflowsConfig\TransitionsConfig\Options\Notes\CustomHtmlConfig
    {
        if (null === $this->customHtml) {
            $this->_usedProperties['customHtml'] = true;
            $this->customHtml = new \Symfony\Config\Pimcore\WorkflowsConfig\TransitionsConfig\Options\Notes\CustomHtmlConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "customHtml()" has already been initialized. You cannot pass values the second time you call customHtml().');
        }

        return $this->customHtml;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('commentEnabled', $value)) {
            $this->_usedProperties['commentEnabled'] = true;
            $this->commentEnabled = $value['commentEnabled'];
            unset($value['commentEnabled']);
        }

        if (array_key_exists('commentRequired', $value)) {
            $this->_usedProperties['commentRequired'] = true;
            $this->commentRequired = $value['commentRequired'];
            unset($value['commentRequired']);
        }

        if (array_key_exists('commentSetterFn', $value)) {
            $this->_usedProperties['commentSetterFn'] = true;
            $this->commentSetterFn = $value['commentSetterFn'];
            unset($value['commentSetterFn']);
        }

        if (array_key_exists('commentGetterFn', $value)) {
            $this->_usedProperties['commentGetterFn'] = true;
            $this->commentGetterFn = $value['commentGetterFn'];
            unset($value['commentGetterFn']);
        }

        if (array_key_exists('type', $value)) {
            $this->_usedProperties['type'] = true;
            $this->type = $value['type'];
            unset($value['type']);
        }

        if (array_key_exists('title', $value)) {
            $this->_usedProperties['title'] = true;
            $this->title = $value['title'];
            unset($value['title']);
        }

        if (array_key_exists('additionalFields', $value)) {
            $this->_usedProperties['additionalFields'] = true;
            $this->additionalFields = array_map(fn ($v) => new \Symfony\Config\Pimcore\WorkflowsConfig\TransitionsConfig\Options\Notes\AdditionalFieldsConfig($v), $value['additionalFields']);
            unset($value['additionalFields']);
        }

        if (array_key_exists('customHtml', $value)) {
            $this->_usedProperties['customHtml'] = true;
            $this->customHtml = new \Symfony\Config\Pimcore\WorkflowsConfig\TransitionsConfig\Options\Notes\CustomHtmlConfig($value['customHtml']);
            unset($value['customHtml']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['commentEnabled'])) {
            $output['commentEnabled'] = $this->commentEnabled;
        }
        if (isset($this->_usedProperties['commentRequired'])) {
            $output['commentRequired'] = $this->commentRequired;
        }
        if (isset($this->_usedProperties['commentSetterFn'])) {
            $output['commentSetterFn'] = $this->commentSetterFn;
        }
        if (isset($this->_usedProperties['commentGetterFn'])) {
            $output['commentGetterFn'] = $this->commentGetterFn;
        }
        if (isset($this->_usedProperties['type'])) {
            $output['type'] = $this->type;
        }
        if (isset($this->_usedProperties['title'])) {
            $output['title'] = $this->title;
        }
        if (isset($this->_usedProperties['additionalFields'])) {
            $output['additionalFields'] = array_map(fn ($v) => $v->toArray(), $this->additionalFields);
        }
        if (isset($this->_usedProperties['customHtml'])) {
            $output['customHtml'] = $this->customHtml->toArray();
        }

        return $output;
    }

}
