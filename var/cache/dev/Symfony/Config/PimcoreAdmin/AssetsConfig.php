<?php

namespace Symfony\Config\PimcoreAdmin;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'NotesEventsConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class AssetsConfig 
{
    private $notesEvents;
    private $hideEditImage;
    private $disableTreePreview;
    private $_usedProperties = [];

    /**
     * @default {"types":["","content","seo","warning","notice"]}
    */
    public function notesEvents(array $value = []): \Symfony\Config\PimcoreAdmin\Assets\NotesEventsConfig
    {
        if (null === $this->notesEvents) {
            $this->_usedProperties['notesEvents'] = true;
            $this->notesEvents = new \Symfony\Config\PimcoreAdmin\Assets\NotesEventsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "notesEvents()" has already been initialized. You cannot pass values the second time you call notesEvents().');
        }

        return $this->notesEvents;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function hideEditImage($value): static
    {
        $this->_usedProperties['hideEditImage'] = true;
        $this->hideEditImage = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function disableTreePreview($value): static
    {
        $this->_usedProperties['disableTreePreview'] = true;
        $this->disableTreePreview = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('notes_events', $value)) {
            $this->_usedProperties['notesEvents'] = true;
            $this->notesEvents = new \Symfony\Config\PimcoreAdmin\Assets\NotesEventsConfig($value['notes_events']);
            unset($value['notes_events']);
        }

        if (array_key_exists('hide_edit_image', $value)) {
            $this->_usedProperties['hideEditImage'] = true;
            $this->hideEditImage = $value['hide_edit_image'];
            unset($value['hide_edit_image']);
        }

        if (array_key_exists('disable_tree_preview', $value)) {
            $this->_usedProperties['disableTreePreview'] = true;
            $this->disableTreePreview = $value['disable_tree_preview'];
            unset($value['disable_tree_preview']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['notesEvents'])) {
            $output['notes_events'] = $this->notesEvents->toArray();
        }
        if (isset($this->_usedProperties['hideEditImage'])) {
            $output['hide_edit_image'] = $this->hideEditImage;
        }
        if (isset($this->_usedProperties['disableTreePreview'])) {
            $output['disable_tree_preview'] = $this->disableTreePreview;
        }

        return $output;
    }

}
