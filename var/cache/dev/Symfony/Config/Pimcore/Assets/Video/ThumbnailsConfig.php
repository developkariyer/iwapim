<?php

namespace Symfony\Config\Pimcore\Assets\Video;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Thumbnails'.\DIRECTORY_SEPARATOR.'DefinitionsConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ThumbnailsConfig 
{
    private $definitions;
    private $autoClearTempFiles;
    private $_usedProperties = [];

    public function definitions(array $value = []): \Symfony\Config\Pimcore\Assets\Video\Thumbnails\DefinitionsConfig
    {
        $this->_usedProperties['definitions'] = true;

        return $this->definitions[] = new \Symfony\Config\Pimcore\Assets\Video\Thumbnails\DefinitionsConfig($value);
    }

    /**
     * Automatically delete all video thumbnail files any time an image or its metadata is updated.
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function autoClearTempFiles($value): static
    {
        $this->_usedProperties['autoClearTempFiles'] = true;
        $this->autoClearTempFiles = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('definitions', $value)) {
            $this->_usedProperties['definitions'] = true;
            $this->definitions = array_map(fn ($v) => new \Symfony\Config\Pimcore\Assets\Video\Thumbnails\DefinitionsConfig($v), $value['definitions']);
            unset($value['definitions']);
        }

        if (array_key_exists('auto_clear_temp_files', $value)) {
            $this->_usedProperties['autoClearTempFiles'] = true;
            $this->autoClearTempFiles = $value['auto_clear_temp_files'];
            unset($value['auto_clear_temp_files']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['definitions'])) {
            $output['definitions'] = array_map(fn ($v) => $v->toArray(), $this->definitions);
        }
        if (isset($this->_usedProperties['autoClearTempFiles'])) {
            $output['auto_clear_temp_files'] = $this->autoClearTempFiles;
        }

        return $output;
    }

}
