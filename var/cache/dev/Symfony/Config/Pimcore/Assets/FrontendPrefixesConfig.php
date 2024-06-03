<?php

namespace Symfony\Config\Pimcore\Assets;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class FrontendPrefixesConfig 
{
    private $source;
    private $thumbnail;
    private $thumbnailDeferred;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function source($value): static
    {
        $this->_usedProperties['source'] = true;
        $this->source = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function thumbnail($value): static
    {
        $this->_usedProperties['thumbnail'] = true;
        $this->thumbnail = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function thumbnailDeferred($value): static
    {
        $this->_usedProperties['thumbnailDeferred'] = true;
        $this->thumbnailDeferred = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('source', $value)) {
            $this->_usedProperties['source'] = true;
            $this->source = $value['source'];
            unset($value['source']);
        }

        if (array_key_exists('thumbnail', $value)) {
            $this->_usedProperties['thumbnail'] = true;
            $this->thumbnail = $value['thumbnail'];
            unset($value['thumbnail']);
        }

        if (array_key_exists('thumbnail_deferred', $value)) {
            $this->_usedProperties['thumbnailDeferred'] = true;
            $this->thumbnailDeferred = $value['thumbnail_deferred'];
            unset($value['thumbnail_deferred']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['source'])) {
            $output['source'] = $this->source;
        }
        if (isset($this->_usedProperties['thumbnail'])) {
            $output['thumbnail'] = $this->thumbnail;
        }
        if (isset($this->_usedProperties['thumbnailDeferred'])) {
            $output['thumbnail_deferred'] = $this->thumbnailDeferred;
        }

        return $output;
    }

}
