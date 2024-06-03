<?php

namespace Symfony\Config\Pimcore;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Email'.\DIRECTORY_SEPARATOR.'SenderConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Email'.\DIRECTORY_SEPARATOR.'ReturnConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Email'.\DIRECTORY_SEPARATOR.'DebugConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class EmailConfig 
{
    private $sender;
    private $return;
    private $debug;
    private $usespecific;
    private $_usedProperties = [];

    /**
     * @default {"name":"","email":""}
    */
    public function sender(array $value = []): \Symfony\Config\Pimcore\Email\SenderConfig
    {
        if (null === $this->sender) {
            $this->_usedProperties['sender'] = true;
            $this->sender = new \Symfony\Config\Pimcore\Email\SenderConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "sender()" has already been initialized. You cannot pass values the second time you call sender().');
        }

        return $this->sender;
    }

    /**
     * @default {"name":"","email":""}
    */
    public function return(array $value = []): \Symfony\Config\Pimcore\Email\ReturnConfig
    {
        if (null === $this->return) {
            $this->_usedProperties['return'] = true;
            $this->return = new \Symfony\Config\Pimcore\Email\ReturnConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "return()" has already been initialized. You cannot pass values the second time you call return().');
        }

        return $this->return;
    }

    /**
     * @default {"email_addresses":""}
    */
    public function debug(array $value = []): \Symfony\Config\Pimcore\Email\DebugConfig
    {
        if (null === $this->debug) {
            $this->_usedProperties['debug'] = true;
            $this->debug = new \Symfony\Config\Pimcore\Email\DebugConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "debug()" has already been initialized. You cannot pass values the second time you call debug().');
        }

        return $this->debug;
    }

    /**
     * @default false
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function usespecific($value): static
    {
        $this->_usedProperties['usespecific'] = true;
        $this->usespecific = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('sender', $value)) {
            $this->_usedProperties['sender'] = true;
            $this->sender = new \Symfony\Config\Pimcore\Email\SenderConfig($value['sender']);
            unset($value['sender']);
        }

        if (array_key_exists('return', $value)) {
            $this->_usedProperties['return'] = true;
            $this->return = new \Symfony\Config\Pimcore\Email\ReturnConfig($value['return']);
            unset($value['return']);
        }

        if (array_key_exists('debug', $value)) {
            $this->_usedProperties['debug'] = true;
            $this->debug = new \Symfony\Config\Pimcore\Email\DebugConfig($value['debug']);
            unset($value['debug']);
        }

        if (array_key_exists('usespecific', $value)) {
            $this->_usedProperties['usespecific'] = true;
            $this->usespecific = $value['usespecific'];
            unset($value['usespecific']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['sender'])) {
            $output['sender'] = $this->sender->toArray();
        }
        if (isset($this->_usedProperties['return'])) {
            $output['return'] = $this->return->toArray();
        }
        if (isset($this->_usedProperties['debug'])) {
            $output['debug'] = $this->debug->toArray();
        }
        if (isset($this->_usedProperties['usespecific'])) {
            $output['usespecific'] = $this->usespecific;
        }

        return $output;
    }

}
