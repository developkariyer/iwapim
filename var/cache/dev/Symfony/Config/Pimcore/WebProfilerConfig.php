<?php

namespace Symfony\Config\Pimcore;

require_once __DIR__.\DIRECTORY_SEPARATOR.'WebProfiler'.\DIRECTORY_SEPARATOR.'ToolbarConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class WebProfilerConfig 
{
    private $toolbar;
    private $_usedProperties = [];

    /**
     * @default {"excluded_routes":[]}
    */
    public function toolbar(array $value = []): \Symfony\Config\Pimcore\WebProfiler\ToolbarConfig
    {
        if (null === $this->toolbar) {
            $this->_usedProperties['toolbar'] = true;
            $this->toolbar = new \Symfony\Config\Pimcore\WebProfiler\ToolbarConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "toolbar()" has already been initialized. You cannot pass values the second time you call toolbar().');
        }

        return $this->toolbar;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('toolbar', $value)) {
            $this->_usedProperties['toolbar'] = true;
            $this->toolbar = new \Symfony\Config\Pimcore\WebProfiler\ToolbarConfig($value['toolbar']);
            unset($value['toolbar']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['toolbar'])) {
            $output['toolbar'] = $this->toolbar->toArray();
        }

        return $output;
    }

}
