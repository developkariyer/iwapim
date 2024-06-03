<?php

namespace Symfony\Config\Pimcore;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Applicationlog'.\DIRECTORY_SEPARATOR.'MailNotificationConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class ApplicationlogConfig 
{
    private $mailNotification;
    private $archiveTreshold;
    private $archiveAlternativeDatabase;
    private $deleteArchiveThreshold;
    private $_usedProperties = [];

    public function mailNotification(array $value = []): \Symfony\Config\Pimcore\Applicationlog\MailNotificationConfig
    {
        if (null === $this->mailNotification) {
            $this->_usedProperties['mailNotification'] = true;
            $this->mailNotification = new \Symfony\Config\Pimcore\Applicationlog\MailNotificationConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "mailNotification()" has already been initialized. You cannot pass values the second time you call mailNotification().');
        }

        return $this->mailNotification;
    }

    /**
     * Archive threshold in days
     * @default 30
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function archiveTreshold($value): static
    {
        $this->_usedProperties['archiveTreshold'] = true;
        $this->archiveTreshold = $value;

        return $this;
    }

    /**
     * Archive database name (optional). Tables will get archived to a different database, recommended when huge amounts of logs will be generated
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function archiveAlternativeDatabase($value): static
    {
        $this->_usedProperties['archiveAlternativeDatabase'] = true;
        $this->archiveAlternativeDatabase = $value;

        return $this;
    }

    /**
     * Threshold for deleting application log archive tables (in months)
     * @default '6'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function deleteArchiveThreshold($value): static
    {
        $this->_usedProperties['deleteArchiveThreshold'] = true;
        $this->deleteArchiveThreshold = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('mail_notification', $value)) {
            $this->_usedProperties['mailNotification'] = true;
            $this->mailNotification = new \Symfony\Config\Pimcore\Applicationlog\MailNotificationConfig($value['mail_notification']);
            unset($value['mail_notification']);
        }

        if (array_key_exists('archive_treshold', $value)) {
            $this->_usedProperties['archiveTreshold'] = true;
            $this->archiveTreshold = $value['archive_treshold'];
            unset($value['archive_treshold']);
        }

        if (array_key_exists('archive_alternative_database', $value)) {
            $this->_usedProperties['archiveAlternativeDatabase'] = true;
            $this->archiveAlternativeDatabase = $value['archive_alternative_database'];
            unset($value['archive_alternative_database']);
        }

        if (array_key_exists('delete_archive_threshold', $value)) {
            $this->_usedProperties['deleteArchiveThreshold'] = true;
            $this->deleteArchiveThreshold = $value['delete_archive_threshold'];
            unset($value['delete_archive_threshold']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['mailNotification'])) {
            $output['mail_notification'] = $this->mailNotification->toArray();
        }
        if (isset($this->_usedProperties['archiveTreshold'])) {
            $output['archive_treshold'] = $this->archiveTreshold;
        }
        if (isset($this->_usedProperties['archiveAlternativeDatabase'])) {
            $output['archive_alternative_database'] = $this->archiveAlternativeDatabase;
        }
        if (isset($this->_usedProperties['deleteArchiveThreshold'])) {
            $output['delete_archive_threshold'] = $this->deleteArchiveThreshold;
        }

        return $output;
    }

}
