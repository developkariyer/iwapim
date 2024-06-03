<?php

namespace Symfony\Config\Pimcore\Assets\Image;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Thumbnails'.\DIRECTORY_SEPARATOR.'DefinitionsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Thumbnails'.\DIRECTORY_SEPARATOR.'ImageOptimizersConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Thumbnails'.\DIRECTORY_SEPARATOR.'AutoFormatsConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ThumbnailsConfig 
{
    private $definitions;
    private $clipAutoSupport;
    private $imageOptimizers;
    private $autoFormats;
    private $statusCache;
    private $autoClearTempFiles;
    private $_usedProperties = [];

    public function definitions(array $value = []): \Symfony\Config\Pimcore\Assets\Image\Thumbnails\DefinitionsConfig
    {
        $this->_usedProperties['definitions'] = true;

        return $this->definitions[] = new \Symfony\Config\Pimcore\Assets\Image\Thumbnails\DefinitionsConfig($value);
    }

    /**
     * Try to detect and use clipping paths and masks in images when generating thumbnails.
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function clipAutoSupport($value): static
    {
        $this->_usedProperties['clipAutoSupport'] = true;
        $this->clipAutoSupport = $value;

        return $this;
    }

    /**
     * @default {"enabled":true}
    */
    public function imageOptimizers(array $value = []): \Symfony\Config\Pimcore\Assets\Image\Thumbnails\ImageOptimizersConfig
    {
        if (null === $this->imageOptimizers) {
            $this->_usedProperties['imageOptimizers'] = true;
            $this->imageOptimizers = new \Symfony\Config\Pimcore\Assets\Image\Thumbnails\ImageOptimizersConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "imageOptimizers()" has already been initialized. You cannot pass values the second time you call imageOptimizers().');
        }

        return $this->imageOptimizers;
    }

    /**
     * @default {"avif":{"enabled":true,"quality":50},"webp":{"enabled":true,"quality":null}}
    */
    public function autoFormats(array $value = []): \Symfony\Config\Pimcore\Assets\Image\Thumbnails\AutoFormatsConfig
    {
        $this->_usedProperties['autoFormats'] = true;

        return $this->autoFormats[] = new \Symfony\Config\Pimcore\Assets\Image\Thumbnails\AutoFormatsConfig($value);
    }

    /**
     * Store image metadata such as filename and modification date in assets_image_thumbnail_cache, this is helpful when using remote object storage for thumbnails.
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function statusCache($value): static
    {
        $this->_usedProperties['statusCache'] = true;
        $this->statusCache = $value;

        return $this;
    }

    /**
     * Automatically delete all image thumbnail files any time an image or its metadata is updated.
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
            $this->definitions = array_map(fn ($v) => new \Symfony\Config\Pimcore\Assets\Image\Thumbnails\DefinitionsConfig($v), $value['definitions']);
            unset($value['definitions']);
        }

        if (array_key_exists('clip_auto_support', $value)) {
            $this->_usedProperties['clipAutoSupport'] = true;
            $this->clipAutoSupport = $value['clip_auto_support'];
            unset($value['clip_auto_support']);
        }

        if (array_key_exists('image_optimizers', $value)) {
            $this->_usedProperties['imageOptimizers'] = true;
            $this->imageOptimizers = new \Symfony\Config\Pimcore\Assets\Image\Thumbnails\ImageOptimizersConfig($value['image_optimizers']);
            unset($value['image_optimizers']);
        }

        if (array_key_exists('auto_formats', $value)) {
            $this->_usedProperties['autoFormats'] = true;
            $this->autoFormats = array_map(fn ($v) => new \Symfony\Config\Pimcore\Assets\Image\Thumbnails\AutoFormatsConfig($v), $value['auto_formats']);
            unset($value['auto_formats']);
        }

        if (array_key_exists('status_cache', $value)) {
            $this->_usedProperties['statusCache'] = true;
            $this->statusCache = $value['status_cache'];
            unset($value['status_cache']);
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
        if (isset($this->_usedProperties['clipAutoSupport'])) {
            $output['clip_auto_support'] = $this->clipAutoSupport;
        }
        if (isset($this->_usedProperties['imageOptimizers'])) {
            $output['image_optimizers'] = $this->imageOptimizers->toArray();
        }
        if (isset($this->_usedProperties['autoFormats'])) {
            $output['auto_formats'] = array_map(fn ($v) => $v->toArray(), $this->autoFormats);
        }
        if (isset($this->_usedProperties['statusCache'])) {
            $output['status_cache'] = $this->statusCache;
        }
        if (isset($this->_usedProperties['autoClearTempFiles'])) {
            $output['auto_clear_temp_files'] = $this->autoClearTempFiles;
        }

        return $output;
    }

}
