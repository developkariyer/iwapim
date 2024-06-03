<?php

namespace Symfony\Config\Pimcore;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class GotenbergConfig 
{
    private $baseUrl;
    private $_usedProperties = [];

    /**
     * @default 'http://gotenberg:3000'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function baseUrl($value): static
    {
        $this->_usedProperties['baseUrl'] = true;
        $this->baseUrl = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('base_url', $value)) {
            $this->_usedProperties['baseUrl'] = true;
            $this->baseUrl = $value['base_url'];
            unset($value['base_url']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['baseUrl'])) {
            $output['base_url'] = $this->baseUrl;
        }

        return $output;
    }

}
