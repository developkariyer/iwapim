<?php

namespace Symfony\Config\Pimcore\TemplatingEngine;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Twig'.\DIRECTORY_SEPARATOR.'SandboxSecurityPolicyConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class TwigConfig 
{
    private $sandboxSecurityPolicy;
    private $_usedProperties = [];

    /**
     * Allowlist tags, filters & functions for evaluating twig
                            templates in a sandbox environment e.g. used by Mailer & Text layout component.
    */
    public function sandboxSecurityPolicy(array $value = []): \Symfony\Config\Pimcore\TemplatingEngine\Twig\SandboxSecurityPolicyConfig
    {
        if (null === $this->sandboxSecurityPolicy) {
            $this->_usedProperties['sandboxSecurityPolicy'] = true;
            $this->sandboxSecurityPolicy = new \Symfony\Config\Pimcore\TemplatingEngine\Twig\SandboxSecurityPolicyConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "sandboxSecurityPolicy()" has already been initialized. You cannot pass values the second time you call sandboxSecurityPolicy().');
        }

        return $this->sandboxSecurityPolicy;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('sandbox_security_policy', $value)) {
            $this->_usedProperties['sandboxSecurityPolicy'] = true;
            $this->sandboxSecurityPolicy = new \Symfony\Config\Pimcore\TemplatingEngine\Twig\SandboxSecurityPolicyConfig($value['sandbox_security_policy']);
            unset($value['sandbox_security_policy']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['sandboxSecurityPolicy'])) {
            $output['sandbox_security_policy'] = $this->sandboxSecurityPolicy->toArray();
        }

        return $output;
    }

}
