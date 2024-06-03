<?php

namespace Symfony\Config\Pimcore\WorkflowsConfig\TransitionsConfig\Options;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class NotificationSettingsConfig 
{
    private $condition;
    private $notifyUsers;
    private $notifyRoles;
    private $channelType;
    private $mailType;
    private $mailPath;
    private $_usedProperties = [];

    /**
     * A symfony expression can be configured here. All sets of notification which are matching the condition will be used.
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
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function notifyUsers(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['notifyUsers'] = true;
        $this->notifyUsers = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function notifyRoles(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['notifyRoles'] = true;
        $this->notifyRoles = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function channelType(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['channelType'] = true;
        $this->channelType = $value;

        return $this;
    }

    /**
     * Type of mail source.
     * @default 'template'
     * @param ParamConfigurator|'template'|'pimcore_document' $value
     * @return $this
     */
    public function mailType($value): static
    {
        $this->_usedProperties['mailType'] = true;
        $this->mailType = $value;

        return $this;
    }

    /**
     * Path to mail source - either Symfony path to template or fullpath to Pimcore document. Optional use %_locale% as placeholder for language.
     * @default '@PimcoreCore/Workflow/NotificationEmail/notificationEmail.html.twig'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function mailPath($value): static
    {
        $this->_usedProperties['mailPath'] = true;
        $this->mailPath = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('condition', $value)) {
            $this->_usedProperties['condition'] = true;
            $this->condition = $value['condition'];
            unset($value['condition']);
        }

        if (array_key_exists('notifyUsers', $value)) {
            $this->_usedProperties['notifyUsers'] = true;
            $this->notifyUsers = $value['notifyUsers'];
            unset($value['notifyUsers']);
        }

        if (array_key_exists('notifyRoles', $value)) {
            $this->_usedProperties['notifyRoles'] = true;
            $this->notifyRoles = $value['notifyRoles'];
            unset($value['notifyRoles']);
        }

        if (array_key_exists('channelType', $value)) {
            $this->_usedProperties['channelType'] = true;
            $this->channelType = $value['channelType'];
            unset($value['channelType']);
        }

        if (array_key_exists('mailType', $value)) {
            $this->_usedProperties['mailType'] = true;
            $this->mailType = $value['mailType'];
            unset($value['mailType']);
        }

        if (array_key_exists('mailPath', $value)) {
            $this->_usedProperties['mailPath'] = true;
            $this->mailPath = $value['mailPath'];
            unset($value['mailPath']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['condition'])) {
            $output['condition'] = $this->condition;
        }
        if (isset($this->_usedProperties['notifyUsers'])) {
            $output['notifyUsers'] = $this->notifyUsers;
        }
        if (isset($this->_usedProperties['notifyRoles'])) {
            $output['notifyRoles'] = $this->notifyRoles;
        }
        if (isset($this->_usedProperties['channelType'])) {
            $output['channelType'] = $this->channelType;
        }
        if (isset($this->_usedProperties['mailType'])) {
            $output['mailType'] = $this->mailType;
        }
        if (isset($this->_usedProperties['mailPath'])) {
            $output['mailPath'] = $this->mailPath;
        }

        return $output;
    }

}
