<?php

namespace Symfony\Config\PimcoreAdmin\Assets;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class NotesEventsConfig 
{
    private $types;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function types(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['types'] = true;
        $this->types = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('types', $value)) {
            $this->_usedProperties['types'] = true;
            $this->types = $value['types'];
            unset($value['types']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['types'])) {
            $output['types'] = $this->types;
        }

        return $output;
    }

}
