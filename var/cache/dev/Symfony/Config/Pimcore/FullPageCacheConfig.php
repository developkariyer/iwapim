<?php

namespace Symfony\Config\Pimcore;

use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class FullPageCacheConfig 
{
    private $enabled;
    private $lifetime;
    private $excludePatterns;
    private $excludeCookie;
    private $_usedProperties = [];
    private $_extraKeys;

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enabled($value): static
    {
        $this->_usedProperties['enabled'] = true;
        $this->enabled = $value;

        return $this;
    }

    /**
     * Optional output-cache lifetime (in seconds) after the cache expires, if not defined the cache will be cleaned on every action inside the CMS, otherwise not (for high traffic sites)
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function lifetime($value): static
    {
        $this->_usedProperties['lifetime'] = true;
        $this->lifetime = $value;

        return $this;
    }

    /**
     * Regular Expressions like: /^\/dir\/toexclude/
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function excludePatterns($value): static
    {
        $this->_usedProperties['excludePatterns'] = true;
        $this->excludePatterns = $value;

        return $this;
    }

    /**
     * Comma separated list of cookie names, that will automatically disable the full-page cache
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function excludeCookie($value): static
    {
        $this->_usedProperties['excludeCookie'] = true;
        $this->excludeCookie = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('lifetime', $value)) {
            $this->_usedProperties['lifetime'] = true;
            $this->lifetime = $value['lifetime'];
            unset($value['lifetime']);
        }

        if (array_key_exists('exclude_patterns', $value)) {
            $this->_usedProperties['excludePatterns'] = true;
            $this->excludePatterns = $value['exclude_patterns'];
            unset($value['exclude_patterns']);
        }

        if (array_key_exists('exclude_cookie', $value)) {
            $this->_usedProperties['excludeCookie'] = true;
            $this->excludeCookie = $value['exclude_cookie'];
            unset($value['exclude_cookie']);
        }

        $this->_extraKeys = $value;

    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['enabled'])) {
            $output['enabled'] = $this->enabled;
        }
        if (isset($this->_usedProperties['lifetime'])) {
            $output['lifetime'] = $this->lifetime;
        }
        if (isset($this->_usedProperties['excludePatterns'])) {
            $output['exclude_patterns'] = $this->excludePatterns;
        }
        if (isset($this->_usedProperties['excludeCookie'])) {
            $output['exclude_cookie'] = $this->excludeCookie;
        }

        return $output + $this->_extraKeys;
    }

    /**
     * @param ParamConfigurator|mixed $value
     *
     * @return $this
     */
    public function set(string $key, mixed $value): static
    {
        $this->_extraKeys[$key] = $value;

        return $this;
    }

}
