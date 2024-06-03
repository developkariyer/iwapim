<?php

namespace Symfony\Config\Pimcore\WebProfiler;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Toolbar'.\DIRECTORY_SEPARATOR.'ExcludedRoutesConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ToolbarConfig 
{
    private $excludedRoutes;
    private $_usedProperties = [];

    /**
     * @template TValue
     * @param TValue $value
     * @return \Symfony\Config\Pimcore\WebProfiler\Toolbar\ExcludedRoutesConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\Pimcore\WebProfiler\Toolbar\ExcludedRoutesConfig : static)
     */
    public function excludedRoutes(array $value = []): \Symfony\Config\Pimcore\WebProfiler\Toolbar\ExcludedRoutesConfig|static
    {
        $this->_usedProperties['excludedRoutes'] = true;
        if (!\is_array($value)) {
            $this->excludedRoutes[] = $value;

            return $this;
        }

        return $this->excludedRoutes[] = new \Symfony\Config\Pimcore\WebProfiler\Toolbar\ExcludedRoutesConfig($value);
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('excluded_routes', $value)) {
            $this->_usedProperties['excludedRoutes'] = true;
            $this->excludedRoutes = array_map(fn ($v) => \is_array($v) ? new \Symfony\Config\Pimcore\WebProfiler\Toolbar\ExcludedRoutesConfig($v) : $v, $value['excluded_routes']);
            unset($value['excluded_routes']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['excludedRoutes'])) {
            $output['excluded_routes'] = array_map(fn ($v) => $v instanceof \Symfony\Config\Pimcore\WebProfiler\Toolbar\ExcludedRoutesConfig ? $v->toArray() : $v, $this->excludedRoutes);
        }

        return $output;
    }

}
