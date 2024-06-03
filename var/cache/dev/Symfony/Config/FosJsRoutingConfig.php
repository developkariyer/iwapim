<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'FosJsRouting'.\DIRECTORY_SEPARATOR.'CacheControlConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class FosJsRoutingConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $serializer;
    private $routesToExpose;
    private $router;
    private $requestContextBaseUrl;
    private $cacheControl;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function serializer($value): static
    {
        $this->_usedProperties['serializer'] = true;
        $this->serializer = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed>|mixed $value
     *
     * @return $this
     */
    public function routesToExpose(mixed $value): static
    {
        $this->_usedProperties['routesToExpose'] = true;
        $this->routesToExpose = $value;

        return $this;
    }

    /**
     * @default 'router'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function router($value): static
    {
        $this->_usedProperties['router'] = true;
        $this->router = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function requestContextBaseUrl($value): static
    {
        $this->_usedProperties['requestContextBaseUrl'] = true;
        $this->requestContextBaseUrl = $value;

        return $this;
    }

    public function cacheControl(array $value = []): \Symfony\Config\FosJsRouting\CacheControlConfig
    {
        if (null === $this->cacheControl) {
            $this->_usedProperties['cacheControl'] = true;
            $this->cacheControl = new \Symfony\Config\FosJsRouting\CacheControlConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "cacheControl()" has already been initialized. You cannot pass values the second time you call cacheControl().');
        }

        return $this->cacheControl;
    }

    public function getExtensionAlias(): string
    {
        return 'fos_js_routing';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('serializer', $value)) {
            $this->_usedProperties['serializer'] = true;
            $this->serializer = $value['serializer'];
            unset($value['serializer']);
        }

        if (array_key_exists('routes_to_expose', $value)) {
            $this->_usedProperties['routesToExpose'] = true;
            $this->routesToExpose = $value['routes_to_expose'];
            unset($value['routes_to_expose']);
        }

        if (array_key_exists('router', $value)) {
            $this->_usedProperties['router'] = true;
            $this->router = $value['router'];
            unset($value['router']);
        }

        if (array_key_exists('request_context_base_url', $value)) {
            $this->_usedProperties['requestContextBaseUrl'] = true;
            $this->requestContextBaseUrl = $value['request_context_base_url'];
            unset($value['request_context_base_url']);
        }

        if (array_key_exists('cache_control', $value)) {
            $this->_usedProperties['cacheControl'] = true;
            $this->cacheControl = new \Symfony\Config\FosJsRouting\CacheControlConfig($value['cache_control']);
            unset($value['cache_control']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['serializer'])) {
            $output['serializer'] = $this->serializer;
        }
        if (isset($this->_usedProperties['routesToExpose'])) {
            $output['routes_to_expose'] = $this->routesToExpose;
        }
        if (isset($this->_usedProperties['router'])) {
            $output['router'] = $this->router;
        }
        if (isset($this->_usedProperties['requestContextBaseUrl'])) {
            $output['request_context_base_url'] = $this->requestContextBaseUrl;
        }
        if (isset($this->_usedProperties['cacheControl'])) {
            $output['cache_control'] = $this->cacheControl->toArray();
        }

        return $output;
    }

}
