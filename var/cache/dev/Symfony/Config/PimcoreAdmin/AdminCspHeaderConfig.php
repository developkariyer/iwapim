<?php

namespace Symfony\Config\PimcoreAdmin;

require_once __DIR__.\DIRECTORY_SEPARATOR.'AdminCspHeader'.\DIRECTORY_SEPARATOR.'AdditionalUrlsConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class AdminCspHeaderConfig 
{
    private $enabled;
    private $excludePaths;
    private $additionalUrls;
    private $_usedProperties = [];

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
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function excludePaths(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['excludePaths'] = true;
        $this->excludePaths = $value;

        return $this;
    }

    /**
     * @default {"default-src":[],"img-src":[],"script-src":[],"style-src":[],"connect-src":[],"font-src":[],"media-src":[],"frame-src":[]}
    */
    public function additionalUrls(array $value = []): \Symfony\Config\PimcoreAdmin\AdminCspHeader\AdditionalUrlsConfig
    {
        if (null === $this->additionalUrls) {
            $this->_usedProperties['additionalUrls'] = true;
            $this->additionalUrls = new \Symfony\Config\PimcoreAdmin\AdminCspHeader\AdditionalUrlsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "additionalUrls()" has already been initialized. You cannot pass values the second time you call additionalUrls().');
        }

        return $this->additionalUrls;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('exclude_paths', $value)) {
            $this->_usedProperties['excludePaths'] = true;
            $this->excludePaths = $value['exclude_paths'];
            unset($value['exclude_paths']);
        }

        if (array_key_exists('additional_urls', $value)) {
            $this->_usedProperties['additionalUrls'] = true;
            $this->additionalUrls = new \Symfony\Config\PimcoreAdmin\AdminCspHeader\AdditionalUrlsConfig($value['additional_urls']);
            unset($value['additional_urls']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['enabled'])) {
            $output['enabled'] = $this->enabled;
        }
        if (isset($this->_usedProperties['excludePaths'])) {
            $output['exclude_paths'] = $this->excludePaths;
        }
        if (isset($this->_usedProperties['additionalUrls'])) {
            $output['additional_urls'] = $this->additionalUrls->toArray();
        }

        return $output;
    }

}
