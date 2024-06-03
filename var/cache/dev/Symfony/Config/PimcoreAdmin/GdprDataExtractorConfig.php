<?php

namespace Symfony\Config\PimcoreAdmin;

require_once __DIR__.\DIRECTORY_SEPARATOR.'GdprDataExtractor'.\DIRECTORY_SEPARATOR.'DataObjectsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'GdprDataExtractor'.\DIRECTORY_SEPARATOR.'AssetsConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class GdprDataExtractorConfig 
{
    private $dataObjects;
    private $assets;
    private $_usedProperties = [];

    /**
     * Settings for DataObjects DataProvider
     * @default {"classes":[]}
    */
    public function dataObjects(array $value = []): \Symfony\Config\PimcoreAdmin\GdprDataExtractor\DataObjectsConfig
    {
        if (null === $this->dataObjects) {
            $this->_usedProperties['dataObjects'] = true;
            $this->dataObjects = new \Symfony\Config\PimcoreAdmin\GdprDataExtractor\DataObjectsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "dataObjects()" has already been initialized. You cannot pass values the second time you call dataObjects().');
        }

        return $this->dataObjects;
    }

    /**
     * Settings for Assets DataProvider
     * @default {"types":[]}
    */
    public function assets(array $value = []): \Symfony\Config\PimcoreAdmin\GdprDataExtractor\AssetsConfig
    {
        if (null === $this->assets) {
            $this->_usedProperties['assets'] = true;
            $this->assets = new \Symfony\Config\PimcoreAdmin\GdprDataExtractor\AssetsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "assets()" has already been initialized. You cannot pass values the second time you call assets().');
        }

        return $this->assets;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('dataObjects', $value)) {
            $this->_usedProperties['dataObjects'] = true;
            $this->dataObjects = new \Symfony\Config\PimcoreAdmin\GdprDataExtractor\DataObjectsConfig($value['dataObjects']);
            unset($value['dataObjects']);
        }

        if (array_key_exists('assets', $value)) {
            $this->_usedProperties['assets'] = true;
            $this->assets = new \Symfony\Config\PimcoreAdmin\GdprDataExtractor\AssetsConfig($value['assets']);
            unset($value['assets']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['dataObjects'])) {
            $output['dataObjects'] = $this->dataObjects->toArray();
        }
        if (isset($this->_usedProperties['assets'])) {
            $output['assets'] = $this->assets->toArray();
        }

        return $output;
    }

}
