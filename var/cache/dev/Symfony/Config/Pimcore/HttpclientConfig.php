<?php

namespace Symfony\Config\Pimcore;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class HttpclientConfig 
{
    private $adapter;
    private $proxyHost;
    private $proxyPort;
    private $proxyUser;
    private $proxyPass;
    private $_usedProperties = [];

    /**
     * Set to `Proxy` if proxy server should be used
     * @default 'Socket'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function adapter($value): static
    {
        $this->_usedProperties['adapter'] = true;
        $this->adapter = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function proxyHost($value): static
    {
        $this->_usedProperties['proxyHost'] = true;
        $this->proxyHost = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function proxyPort($value): static
    {
        $this->_usedProperties['proxyPort'] = true;
        $this->proxyPort = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function proxyUser($value): static
    {
        $this->_usedProperties['proxyUser'] = true;
        $this->proxyUser = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function proxyPass($value): static
    {
        $this->_usedProperties['proxyPass'] = true;
        $this->proxyPass = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('adapter', $value)) {
            $this->_usedProperties['adapter'] = true;
            $this->adapter = $value['adapter'];
            unset($value['adapter']);
        }

        if (array_key_exists('proxy_host', $value)) {
            $this->_usedProperties['proxyHost'] = true;
            $this->proxyHost = $value['proxy_host'];
            unset($value['proxy_host']);
        }

        if (array_key_exists('proxy_port', $value)) {
            $this->_usedProperties['proxyPort'] = true;
            $this->proxyPort = $value['proxy_port'];
            unset($value['proxy_port']);
        }

        if (array_key_exists('proxy_user', $value)) {
            $this->_usedProperties['proxyUser'] = true;
            $this->proxyUser = $value['proxy_user'];
            unset($value['proxy_user']);
        }

        if (array_key_exists('proxy_pass', $value)) {
            $this->_usedProperties['proxyPass'] = true;
            $this->proxyPass = $value['proxy_pass'];
            unset($value['proxy_pass']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['adapter'])) {
            $output['adapter'] = $this->adapter;
        }
        if (isset($this->_usedProperties['proxyHost'])) {
            $output['proxy_host'] = $this->proxyHost;
        }
        if (isset($this->_usedProperties['proxyPort'])) {
            $output['proxy_port'] = $this->proxyPort;
        }
        if (isset($this->_usedProperties['proxyUser'])) {
            $output['proxy_user'] = $this->proxyUser;
        }
        if (isset($this->_usedProperties['proxyPass'])) {
            $output['proxy_pass'] = $this->proxyPass;
        }

        return $output;
    }

}
