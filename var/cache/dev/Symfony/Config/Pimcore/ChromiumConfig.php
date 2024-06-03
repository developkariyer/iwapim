<?php

namespace Symfony\Config\Pimcore;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ChromiumConfig 
{
    private $uri;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function uri($value): static
    {
        $this->_usedProperties['uri'] = true;
        $this->uri = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('uri', $value)) {
            $this->_usedProperties['uri'] = true;
            $this->uri = $value['uri'];
            unset($value['uri']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['uri'])) {
            $output['uri'] = $this->uri;
        }

        return $output;
    }

}
