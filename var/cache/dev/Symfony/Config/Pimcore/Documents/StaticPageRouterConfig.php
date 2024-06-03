<?php

namespace Symfony\Config\Pimcore\Documents;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class StaticPageRouterConfig 
{
    private $enabled;
    private $routePattern;
    private $_usedProperties = [];

    /**
     * Enable Static Page router for document when using remote storage for generated pages
     * @default false
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
     * Optionally define route patterns to lookup static pages. Regular Expressions like: /^\/en\/Magazine/
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function routePattern($value): static
    {
        $this->_usedProperties['routePattern'] = true;
        $this->routePattern = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('route_pattern', $value)) {
            $this->_usedProperties['routePattern'] = true;
            $this->routePattern = $value['route_pattern'];
            unset($value['route_pattern']);
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
        if (isset($this->_usedProperties['routePattern'])) {
            $output['route_pattern'] = $this->routePattern;
        }

        return $output;
    }

}
