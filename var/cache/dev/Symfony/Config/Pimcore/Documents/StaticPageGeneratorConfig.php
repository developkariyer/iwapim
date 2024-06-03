<?php

namespace Symfony\Config\Pimcore\Documents;

require_once __DIR__.\DIRECTORY_SEPARATOR.'StaticPageGenerator'.\DIRECTORY_SEPARATOR.'HeadersConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class StaticPageGeneratorConfig 
{
    private $useMainDomain;
    private $headers;
    private $_usedProperties = [];

    /**
     * Use main domain for static pages folder in tmp/pages
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function useMainDomain($value): static
    {
        $this->_usedProperties['useMainDomain'] = true;
        $this->useMainDomain = $value;

        return $this;
    }

    public function headers(array $value = []): \Symfony\Config\Pimcore\Documents\StaticPageGenerator\HeadersConfig
    {
        $this->_usedProperties['headers'] = true;

        return $this->headers[] = new \Symfony\Config\Pimcore\Documents\StaticPageGenerator\HeadersConfig($value);
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('use_main_domain', $value)) {
            $this->_usedProperties['useMainDomain'] = true;
            $this->useMainDomain = $value['use_main_domain'];
            unset($value['use_main_domain']);
        }

        if (array_key_exists('headers', $value)) {
            $this->_usedProperties['headers'] = true;
            $this->headers = array_map(fn ($v) => new \Symfony\Config\Pimcore\Documents\StaticPageGenerator\HeadersConfig($v), $value['headers']);
            unset($value['headers']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['useMainDomain'])) {
            $output['use_main_domain'] = $this->useMainDomain;
        }
        if (isset($this->_usedProperties['headers'])) {
            $output['headers'] = array_map(fn ($v) => $v->toArray(), $this->headers);
        }

        return $output;
    }

}
