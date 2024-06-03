<?php

namespace Symfony\Config\Pimcore\Applicationlog;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class MailNotificationConfig 
{
    private $sendLogSummary;
    private $filterPriority;
    private $mailReceiver;
    private $_usedProperties = [];

    /**
     * Send log summary via email
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function sendLogSummary($value): static
    {
        $this->_usedProperties['sendLogSummary'] = true;
        $this->sendLogSummary = $value;

        return $this;
    }

    /**
     * Filter threshold for email summary, choose one of: 7 (debug), 6 (info), 5 (notice), 4 (warning), 3 (error), 2 (critical), 1 (alert) ,0 (emerg)
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function filterPriority($value): static
    {
        $this->_usedProperties['filterPriority'] = true;
        $this->filterPriority = $value;

        return $this;
    }

    /**
     * Log summary receivers. Separate multiple email receivers by using ;
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function mailReceiver($value): static
    {
        $this->_usedProperties['mailReceiver'] = true;
        $this->mailReceiver = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('send_log_summary', $value)) {
            $this->_usedProperties['sendLogSummary'] = true;
            $this->sendLogSummary = $value['send_log_summary'];
            unset($value['send_log_summary']);
        }

        if (array_key_exists('filter_priority', $value)) {
            $this->_usedProperties['filterPriority'] = true;
            $this->filterPriority = $value['filter_priority'];
            unset($value['filter_priority']);
        }

        if (array_key_exists('mail_receiver', $value)) {
            $this->_usedProperties['mailReceiver'] = true;
            $this->mailReceiver = $value['mail_receiver'];
            unset($value['mail_receiver']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['sendLogSummary'])) {
            $output['send_log_summary'] = $this->sendLogSummary;
        }
        if (isset($this->_usedProperties['filterPriority'])) {
            $output['filter_priority'] = $this->filterPriority;
        }
        if (isset($this->_usedProperties['mailReceiver'])) {
            $output['mail_receiver'] = $this->mailReceiver;
        }

        return $output;
    }

}
