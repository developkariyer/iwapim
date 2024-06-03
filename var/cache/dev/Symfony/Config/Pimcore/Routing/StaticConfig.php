<?php

namespace Symfony\Config\Pimcore\Routing;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class StaticConfig 
{
    private $localeParams;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function localeParams(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['localeParams'] = true;
        $this->localeParams = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('locale_params', $value)) {
            $this->_usedProperties['localeParams'] = true;
            $this->localeParams = $value['locale_params'];
            unset($value['locale_params']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['localeParams'])) {
            $output['locale_params'] = $this->localeParams;
        }

        return $output;
    }

}
