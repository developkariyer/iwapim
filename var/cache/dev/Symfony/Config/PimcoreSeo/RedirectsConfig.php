<?php

namespace Symfony\Config\PimcoreSeo;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class RedirectsConfig 
{
    private $statusCodes;
    private $autoCreateRedirects;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function statusCodes(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['statusCodes'] = true;
        $this->statusCodes = $value;

        return $this;
    }

    /**
     * Auto create redirects on moving documents & changing pretty url, updating Url slugs in Data Objects.
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function autoCreateRedirects($value): static
    {
        $this->_usedProperties['autoCreateRedirects'] = true;
        $this->autoCreateRedirects = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('status_codes', $value)) {
            $this->_usedProperties['statusCodes'] = true;
            $this->statusCodes = $value['status_codes'];
            unset($value['status_codes']);
        }

        if (array_key_exists('auto_create_redirects', $value)) {
            $this->_usedProperties['autoCreateRedirects'] = true;
            $this->autoCreateRedirects = $value['auto_create_redirects'];
            unset($value['auto_create_redirects']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['statusCodes'])) {
            $output['status_codes'] = $this->statusCodes;
        }
        if (isset($this->_usedProperties['autoCreateRedirects'])) {
            $output['auto_create_redirects'] = $this->autoCreateRedirects;
        }

        return $output;
    }

}
