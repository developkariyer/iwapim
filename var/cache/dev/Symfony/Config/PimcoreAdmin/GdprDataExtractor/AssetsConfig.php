<?php

namespace Symfony\Config\PimcoreAdmin\GdprDataExtractor;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'TypesConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class AssetsConfig 
{
    private $types;
    private $_usedProperties = [];

    /**
     * Configure which types should be considered
    */
    public function types(array $value = []): \Symfony\Config\PimcoreAdmin\GdprDataExtractor\Assets\TypesConfig
    {
        $this->_usedProperties['types'] = true;

        return $this->types[] = new \Symfony\Config\PimcoreAdmin\GdprDataExtractor\Assets\TypesConfig($value);
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('types', $value)) {
            $this->_usedProperties['types'] = true;
            $this->types = array_map(fn ($v) => new \Symfony\Config\PimcoreAdmin\GdprDataExtractor\Assets\TypesConfig($v), $value['types']);
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
            $output['types'] = array_map(fn ($v) => $v->toArray(), $this->types);
        }

        return $output;
    }

}
