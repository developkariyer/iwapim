<?php

namespace Symfony\Config\Pimcore;

require_once __DIR__.\DIRECTORY_SEPARATOR.'ContextConfig'.\DIRECTORY_SEPARATOR.'RoutesConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ContextConfig 
{
    private $routes;
    private $_usedProperties = [];

    /**
     * @template TValue
     * @param TValue $value
     * @return \Symfony\Config\Pimcore\ContextConfig\RoutesConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\Pimcore\ContextConfig\RoutesConfig : static)
     */
    public function routes(array $value = []): \Symfony\Config\Pimcore\ContextConfig\RoutesConfig|static
    {
        $this->_usedProperties['routes'] = true;
        if (!\is_array($value)) {
            $this->routes[] = $value;

            return $this;
        }

        return $this->routes[] = new \Symfony\Config\Pimcore\ContextConfig\RoutesConfig($value);
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('routes', $value)) {
            $this->_usedProperties['routes'] = true;
            $this->routes = array_map(fn ($v) => \is_array($v) ? new \Symfony\Config\Pimcore\ContextConfig\RoutesConfig($v) : $v, $value['routes']);
            unset($value['routes']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['routes'])) {
            $output['routes'] = array_map(fn ($v) => $v instanceof \Symfony\Config\Pimcore\ContextConfig\RoutesConfig ? $v->toArray() : $v, $this->routes);
        }

        return $output;
    }

}
