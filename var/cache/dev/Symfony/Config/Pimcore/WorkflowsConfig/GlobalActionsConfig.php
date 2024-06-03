<?php

namespace Symfony\Config\Pimcore\WorkflowsConfig;

require_once __DIR__.\DIRECTORY_SEPARATOR.'GlobalActionsConfig'.\DIRECTORY_SEPARATOR.'NotesConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class GlobalActionsConfig 
{
    private $label;
    private $iconClass;
    private $objectLayout;
    private $guard;
    private $saveSubject;
    private $to;
    private $notes;
    private $_usedProperties = [];

    /**
     * Nice name for the Pimcore backend.
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
     * CSS class to define the icon which will be used in the actions button in the backend.
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function iconClass($value): static
    {
        $this->_usedProperties['iconClass'] = true;
        $this->iconClass = $value;

        return $this;
    }

    /**
     * Forces an object layout after the global action was performed. This objectLayout setting overrules all objectLayout settings within the places configs.
     * @default false
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function objectLayout($value): static
    {
        $this->_usedProperties['objectLayout'] = true;
        $this->objectLayout = $value;

        return $this;
    }

    /**
     * An expression to block the action
     * @example is_fully_authenticated() and is_granted('ROLE_JOURNALIST') and subject.getTitle() == 'My first article'
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function guard($value): static
    {
        $this->_usedProperties['guard'] = true;
        $this->guard = $value;

        return $this;
    }

    /**
     * Determines if the global action should perform a save on the subject, default behavior is set to true
     * @example false
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function saveSubject($value): static
    {
        $this->_usedProperties['saveSubject'] = true;
        $this->saveSubject = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed>|string $value
     *
     * @return $this
     */
    public function to(ParamConfigurator|string|array $value): static
    {
        $this->_usedProperties['to'] = true;
        $this->to = $value;

        return $this;
    }

    /**
     * See notes section of transitions. It works exactly the same way.
    */
    public function notes(array $value = []): \Symfony\Config\Pimcore\WorkflowsConfig\GlobalActionsConfig\NotesConfig
    {
        if (null === $this->notes) {
            $this->_usedProperties['notes'] = true;
            $this->notes = new \Symfony\Config\Pimcore\WorkflowsConfig\GlobalActionsConfig\NotesConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "notes()" has already been initialized. You cannot pass values the second time you call notes().');
        }

        return $this->notes;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('label', $value)) {
            $this->_usedProperties['label'] = true;
            $this->label = $value['label'];
            unset($value['label']);
        }

        if (array_key_exists('iconClass', $value)) {
            $this->_usedProperties['iconClass'] = true;
            $this->iconClass = $value['iconClass'];
            unset($value['iconClass']);
        }

        if (array_key_exists('objectLayout', $value)) {
            $this->_usedProperties['objectLayout'] = true;
            $this->objectLayout = $value['objectLayout'];
            unset($value['objectLayout']);
        }

        if (array_key_exists('guard', $value)) {
            $this->_usedProperties['guard'] = true;
            $this->guard = $value['guard'];
            unset($value['guard']);
        }

        if (array_key_exists('saveSubject', $value)) {
            $this->_usedProperties['saveSubject'] = true;
            $this->saveSubject = $value['saveSubject'];
            unset($value['saveSubject']);
        }

        if (array_key_exists('to', $value)) {
            $this->_usedProperties['to'] = true;
            $this->to = $value['to'];
            unset($value['to']);
        }

        if (array_key_exists('notes', $value)) {
            $this->_usedProperties['notes'] = true;
            $this->notes = new \Symfony\Config\Pimcore\WorkflowsConfig\GlobalActionsConfig\NotesConfig($value['notes']);
            unset($value['notes']);
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
        if (isset($this->_usedProperties['iconClass'])) {
            $output['iconClass'] = $this->iconClass;
        }
        if (isset($this->_usedProperties['objectLayout'])) {
            $output['objectLayout'] = $this->objectLayout;
        }
        if (isset($this->_usedProperties['guard'])) {
            $output['guard'] = $this->guard;
        }
        if (isset($this->_usedProperties['saveSubject'])) {
            $output['saveSubject'] = $this->saveSubject;
        }
        if (isset($this->_usedProperties['to'])) {
            $output['to'] = $this->to;
        }
        if (isset($this->_usedProperties['notes'])) {
            $output['notes'] = $this->notes->toArray();
        }

        return $output;
    }

}
