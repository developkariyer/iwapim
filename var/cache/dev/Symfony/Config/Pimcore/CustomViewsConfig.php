<?php

namespace Symfony\Config\Pimcore;

require_once __DIR__.\DIRECTORY_SEPARATOR.'CustomViews'.\DIRECTORY_SEPARATOR.'DefinitionsConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class CustomViewsConfig 
{
    private $definitions;
    private $_usedProperties = [];
    private $_extraKeys;

    public function definitions(array $value = []): \Symfony\Config\Pimcore\CustomViews\DefinitionsConfig
    {
        $this->_usedProperties['definitions'] = true;

        return $this->definitions[] = new \Symfony\Config\Pimcore\CustomViews\DefinitionsConfig($value);
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('definitions', $value)) {
            $this->_usedProperties['definitions'] = true;
            $this->definitions = array_map(fn ($v) => new \Symfony\Config\Pimcore\CustomViews\DefinitionsConfig($v), $value['definitions']);
            unset($value['definitions']);
        }

        $this->_extraKeys = $value;

    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['definitions'])) {
            $output['definitions'] = array_map(fn ($v) => $v->toArray(), $this->definitions);
        }

        return $output + $this->_extraKeys;
    }

    /**
     * @param ParamConfigurator|mixed $value
     *
     * @return $this
     */
    public function set(string $key, mixed $value): static
    {
        $this->_extraKeys[$key] = $value;

        return $this;
    }

}
