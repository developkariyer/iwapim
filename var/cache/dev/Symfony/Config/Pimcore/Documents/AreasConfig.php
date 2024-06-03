<?php

namespace Symfony\Config\Pimcore\Documents;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class AreasConfig 
{
    private $autoload;
    private $_usedProperties = [];

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function autoload($value): static
    {
        $this->_usedProperties['autoload'] = true;
        $this->autoload = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('autoload', $value)) {
            $this->_usedProperties['autoload'] = true;
            $this->autoload = $value['autoload'];
            unset($value['autoload']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['autoload'])) {
            $output['autoload'] = $this->autoload;
        }

        return $output;
    }

}
