<?php

namespace Symfony\Config\PimcoreAdmin\GdprDataExtractor;

require_once __DIR__.\DIRECTORY_SEPARATOR.'DataObjects'.\DIRECTORY_SEPARATOR.'ClassesConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DataObjectsConfig 
{
    private $classes;
    private $_usedProperties = [];

    /**
     * Configure which classes should be considered, array key is class name
    */
    public function classes(array $value = []): \Symfony\Config\PimcoreAdmin\GdprDataExtractor\DataObjects\ClassesConfig
    {
        $this->_usedProperties['classes'] = true;

        return $this->classes[] = new \Symfony\Config\PimcoreAdmin\GdprDataExtractor\DataObjects\ClassesConfig($value);
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('classes', $value)) {
            $this->_usedProperties['classes'] = true;
            $this->classes = array_map(fn ($v) => new \Symfony\Config\PimcoreAdmin\GdprDataExtractor\DataObjects\ClassesConfig($v), $value['classes']);
            unset($value['classes']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['classes'])) {
            $output['classes'] = array_map(fn ($v) => $v->toArray(), $this->classes);
        }

        return $output;
    }

}
