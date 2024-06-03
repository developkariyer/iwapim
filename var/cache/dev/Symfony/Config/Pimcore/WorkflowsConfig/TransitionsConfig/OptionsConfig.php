<?php

namespace Symfony\Config\Pimcore\WorkflowsConfig\TransitionsConfig;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Options'.\DIRECTORY_SEPARATOR.'NotesConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Options'.\DIRECTORY_SEPARATOR.'NotificationSettingsConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class OptionsConfig 
{
    private $label;
    private $notes;
    private $iconClass;
    private $objectLayout;
    private $notificationSettings;
    private $changePublishedState;
    private $unsavedChangesBehaviour;
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

    public function notes(array $value = []): \Symfony\Config\Pimcore\WorkflowsConfig\TransitionsConfig\Options\NotesConfig
    {
        if (null === $this->notes) {
            $this->_usedProperties['notes'] = true;
            $this->notes = new \Symfony\Config\Pimcore\WorkflowsConfig\TransitionsConfig\Options\NotesConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "notes()" has already been initialized. You cannot pass values the second time you call notes().');
        }

        return $this->notes;
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
     * Forces an object layout after the transition was performed. This objectLayout setting overrules all objectLayout settings within the places configs.
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

    public function notificationSettings(array $value = []): \Symfony\Config\Pimcore\WorkflowsConfig\TransitionsConfig\Options\NotificationSettingsConfig
    {
        $this->_usedProperties['notificationSettings'] = true;

        return $this->notificationSettings[] = new \Symfony\Config\Pimcore\WorkflowsConfig\TransitionsConfig\Options\NotificationSettingsConfig($value);
    }

    /**
     * Change published state of element while transition (only available for documents and data objects).
     * @default 'no_change'
     * @param ParamConfigurator|'no_change'|'force_unpublished'|'force_published'|'save_version' $value
     * @return $this
     */
    public function changePublishedState($value): static
    {
        $this->_usedProperties['changePublishedState'] = true;
        $this->changePublishedState = $value;

        return $this;
    }

    /**
     * Behaviour when workflow transition gets applied but there are unsaved changes
     * @default 'warn'
     * @param ParamConfigurator|'save'|'warn'|'ignore' $value
     * @return $this
     */
    public function unsavedChangesBehaviour($value): static
    {
        $this->_usedProperties['unsavedChangesBehaviour'] = true;
        $this->unsavedChangesBehaviour = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('label', $value)) {
            $this->_usedProperties['label'] = true;
            $this->label = $value['label'];
            unset($value['label']);
        }

        if (array_key_exists('notes', $value)) {
            $this->_usedProperties['notes'] = true;
            $this->notes = new \Symfony\Config\Pimcore\WorkflowsConfig\TransitionsConfig\Options\NotesConfig($value['notes']);
            unset($value['notes']);
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

        if (array_key_exists('notificationSettings', $value)) {
            $this->_usedProperties['notificationSettings'] = true;
            $this->notificationSettings = array_map(fn ($v) => new \Symfony\Config\Pimcore\WorkflowsConfig\TransitionsConfig\Options\NotificationSettingsConfig($v), $value['notificationSettings']);
            unset($value['notificationSettings']);
        }

        if (array_key_exists('changePublishedState', $value)) {
            $this->_usedProperties['changePublishedState'] = true;
            $this->changePublishedState = $value['changePublishedState'];
            unset($value['changePublishedState']);
        }

        if (array_key_exists('unsavedChangesBehaviour', $value)) {
            $this->_usedProperties['unsavedChangesBehaviour'] = true;
            $this->unsavedChangesBehaviour = $value['unsavedChangesBehaviour'];
            unset($value['unsavedChangesBehaviour']);
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
        if (isset($this->_usedProperties['notes'])) {
            $output['notes'] = $this->notes->toArray();
        }
        if (isset($this->_usedProperties['iconClass'])) {
            $output['iconClass'] = $this->iconClass;
        }
        if (isset($this->_usedProperties['objectLayout'])) {
            $output['objectLayout'] = $this->objectLayout;
        }
        if (isset($this->_usedProperties['notificationSettings'])) {
            $output['notificationSettings'] = array_map(fn ($v) => $v->toArray(), $this->notificationSettings);
        }
        if (isset($this->_usedProperties['changePublishedState'])) {
            $output['changePublishedState'] = $this->changePublishedState;
        }
        if (isset($this->_usedProperties['unsavedChangesBehaviour'])) {
            $output['unsavedChangesBehaviour'] = $this->unsavedChangesBehaviour;
        }

        return $output;
    }

}
