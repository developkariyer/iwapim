<?php

namespace Symfony\Config\Pimcore\TemplatingEngine\Twig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class SandboxSecurityPolicyConfig 
{
    private $tags;
    private $filters;
    private $functions;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function tags(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['tags'] = true;
        $this->tags = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function filters(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['filters'] = true;
        $this->filters = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function functions(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['functions'] = true;
        $this->functions = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('tags', $value)) {
            $this->_usedProperties['tags'] = true;
            $this->tags = $value['tags'];
            unset($value['tags']);
        }

        if (array_key_exists('filters', $value)) {
            $this->_usedProperties['filters'] = true;
            $this->filters = $value['filters'];
            unset($value['filters']);
        }

        if (array_key_exists('functions', $value)) {
            $this->_usedProperties['functions'] = true;
            $this->functions = $value['functions'];
            unset($value['functions']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['tags'])) {
            $output['tags'] = $this->tags;
        }
        if (isset($this->_usedProperties['filters'])) {
            $output['filters'] = $this->filters;
        }
        if (isset($this->_usedProperties['functions'])) {
            $output['functions'] = $this->functions;
        }

        return $output;
    }

}
