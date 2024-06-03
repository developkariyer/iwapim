<?php

namespace Symfony\Config\Pimcore\Email;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DebugConfig 
{
    private $emailAddresses;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function emailAddresses($value): static
    {
        $this->_usedProperties['emailAddresses'] = true;
        $this->emailAddresses = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('email_addresses', $value)) {
            $this->_usedProperties['emailAddresses'] = true;
            $this->emailAddresses = $value['email_addresses'];
            unset($value['email_addresses']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['emailAddresses'])) {
            $output['email_addresses'] = $this->emailAddresses;
        }

        return $output;
    }

}
