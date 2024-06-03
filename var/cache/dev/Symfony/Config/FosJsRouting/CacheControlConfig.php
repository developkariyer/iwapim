<?php

namespace Symfony\Config\FosJsRouting;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class CacheControlConfig 
{
    private $public;
    private $expires;
    private $maxage;
    private $smaxage;
    private $vary;
    private $_usedProperties = [];

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function public($value): static
    {
        $this->_usedProperties['public'] = true;
        $this->public = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function expires($value): static
    {
        $this->_usedProperties['expires'] = true;
        $this->expires = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function maxage($value): static
    {
        $this->_usedProperties['maxage'] = true;
        $this->maxage = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function smaxage($value): static
    {
        $this->_usedProperties['smaxage'] = true;
        $this->smaxage = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed>|mixed $value
     *
     * @return $this
     */
    public function vary(mixed $value): static
    {
        $this->_usedProperties['vary'] = true;
        $this->vary = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('public', $value)) {
            $this->_usedProperties['public'] = true;
            $this->public = $value['public'];
            unset($value['public']);
        }

        if (array_key_exists('expires', $value)) {
            $this->_usedProperties['expires'] = true;
            $this->expires = $value['expires'];
            unset($value['expires']);
        }

        if (array_key_exists('maxage', $value)) {
            $this->_usedProperties['maxage'] = true;
            $this->maxage = $value['maxage'];
            unset($value['maxage']);
        }

        if (array_key_exists('smaxage', $value)) {
            $this->_usedProperties['smaxage'] = true;
            $this->smaxage = $value['smaxage'];
            unset($value['smaxage']);
        }

        if (array_key_exists('vary', $value)) {
            $this->_usedProperties['vary'] = true;
            $this->vary = $value['vary'];
            unset($value['vary']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['public'])) {
            $output['public'] = $this->public;
        }
        if (isset($this->_usedProperties['expires'])) {
            $output['expires'] = $this->expires;
        }
        if (isset($this->_usedProperties['maxage'])) {
            $output['maxage'] = $this->maxage;
        }
        if (isset($this->_usedProperties['smaxage'])) {
            $output['smaxage'] = $this->smaxage;
        }
        if (isset($this->_usedProperties['vary'])) {
            $output['vary'] = $this->vary;
        }

        return $output;
    }

}
