<?php

namespace Symfony\Config\PimcoreSeo;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Sitemaps'.\DIRECTORY_SEPARATOR.'GeneratorsConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class SitemapsConfig 
{
    private $generators;
    private $_usedProperties = [];

    /**
     * @template TValue
     * @param TValue $value
     * @return \Symfony\Config\PimcoreSeo\Sitemaps\GeneratorsConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\PimcoreSeo\Sitemaps\GeneratorsConfig : static)
     */
    public function generators(string $name, string|array $value = []): \Symfony\Config\PimcoreSeo\Sitemaps\GeneratorsConfig|static
    {
        if (!\is_array($value)) {
            $this->_usedProperties['generators'] = true;
            $this->generators[$name] = $value;

            return $this;
        }

        if (!isset($this->generators[$name]) || !$this->generators[$name] instanceof \Symfony\Config\PimcoreSeo\Sitemaps\GeneratorsConfig) {
            $this->_usedProperties['generators'] = true;
            $this->generators[$name] = new \Symfony\Config\PimcoreSeo\Sitemaps\GeneratorsConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "generators()" has already been initialized. You cannot pass values the second time you call generators().');
        }

        return $this->generators[$name];
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('generators', $value)) {
            $this->_usedProperties['generators'] = true;
            $this->generators = array_map(fn ($v) => \is_array($v) ? new \Symfony\Config\PimcoreSeo\Sitemaps\GeneratorsConfig($v) : $v, $value['generators']);
            unset($value['generators']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['generators'])) {
            $output['generators'] = array_map(fn ($v) => $v instanceof \Symfony\Config\PimcoreSeo\Sitemaps\GeneratorsConfig ? $v->toArray() : $v, $this->generators);
        }

        return $output;
    }

}
