<?php

namespace Symfony\Config\PrestaSitemap;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DefaultsConfig 
{
    private $priority;
    private $changefreq;
    private $lastmod;
    private $_usedProperties = [];

    /**
     * @default 0.5
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function priority($value): static
    {
        $this->_usedProperties['priority'] = true;
        $this->priority = $value;

        return $this;
    }

    /**
     * @default 'daily'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function changefreq($value): static
    {
        $this->_usedProperties['changefreq'] = true;
        $this->changefreq = $value;

        return $this;
    }

    /**
     * @default 'now'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function lastmod($value): static
    {
        $this->_usedProperties['lastmod'] = true;
        $this->lastmod = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('priority', $value)) {
            $this->_usedProperties['priority'] = true;
            $this->priority = $value['priority'];
            unset($value['priority']);
        }

        if (array_key_exists('changefreq', $value)) {
            $this->_usedProperties['changefreq'] = true;
            $this->changefreq = $value['changefreq'];
            unset($value['changefreq']);
        }

        if (array_key_exists('lastmod', $value)) {
            $this->_usedProperties['lastmod'] = true;
            $this->lastmod = $value['lastmod'];
            unset($value['lastmod']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['priority'])) {
            $output['priority'] = $this->priority;
        }
        if (isset($this->_usedProperties['changefreq'])) {
            $output['changefreq'] = $this->changefreq;
        }
        if (isset($this->_usedProperties['lastmod'])) {
            $output['lastmod'] = $this->lastmod;
        }

        return $output;
    }

}
