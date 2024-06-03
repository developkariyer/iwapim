<?php

namespace Symfony\Config\Pimcore;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class BundlesConfig 
{
    private $searchPaths;
    private $handleComposer;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function searchPaths(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['searchPaths'] = true;
        $this->searchPaths = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function handleComposer($value): static
    {
        $this->_usedProperties['handleComposer'] = true;
        $this->handleComposer = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('search_paths', $value)) {
            $this->_usedProperties['searchPaths'] = true;
            $this->searchPaths = $value['search_paths'];
            unset($value['search_paths']);
        }

        if (array_key_exists('handle_composer', $value)) {
            $this->_usedProperties['handleComposer'] = true;
            $this->handleComposer = $value['handle_composer'];
            unset($value['handle_composer']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['searchPaths'])) {
            $output['search_paths'] = $this->searchPaths;
        }
        if (isset($this->_usedProperties['handleComposer'])) {
            $output['handle_composer'] = $this->handleComposer;
        }

        return $output;
    }

}
