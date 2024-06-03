<?php

namespace Symfony\Config\Pimcore\Assets\Image\Thumbnails;

require_once __DIR__.\DIRECTORY_SEPARATOR.'DefinitionsConfig'.\DIRECTORY_SEPARATOR.'ItemsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'DefinitionsConfig'.\DIRECTORY_SEPARATOR.'MediasConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DefinitionsConfig 
{
    private $id;
    private $name;
    private $description;
    private $group;
    private $format;
    private $quality;
    private $highResolution;
    private $preserveColor;
    private $preserveMetaData;
    private $rasterizeSVG;
    private $downloadable;
    private $modificationDate;
    private $creationDate;
    private $preserveAnimation;
    private $items;
    private $medias;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function id($value): static
    {
        $this->_usedProperties['id'] = true;
        $this->id = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function name($value): static
    {
        $this->_usedProperties['name'] = true;
        $this->name = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function description($value): static
    {
        $this->_usedProperties['description'] = true;
        $this->description = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function group($value): static
    {
        $this->_usedProperties['group'] = true;
        $this->group = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function format($value): static
    {
        $this->_usedProperties['format'] = true;
        $this->format = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function quality($value): static
    {
        $this->_usedProperties['quality'] = true;
        $this->quality = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function highResolution($value): static
    {
        $this->_usedProperties['highResolution'] = true;
        $this->highResolution = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function preserveColor($value): static
    {
        $this->_usedProperties['preserveColor'] = true;
        $this->preserveColor = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function preserveMetaData($value): static
    {
        $this->_usedProperties['preserveMetaData'] = true;
        $this->preserveMetaData = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function rasterizeSVG($value): static
    {
        $this->_usedProperties['rasterizeSVG'] = true;
        $this->rasterizeSVG = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function downloadable($value): static
    {
        $this->_usedProperties['downloadable'] = true;
        $this->downloadable = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function modificationDate($value): static
    {
        $this->_usedProperties['modificationDate'] = true;
        $this->modificationDate = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function creationDate($value): static
    {
        $this->_usedProperties['creationDate'] = true;
        $this->creationDate = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function preserveAnimation($value): static
    {
        $this->_usedProperties['preserveAnimation'] = true;
        $this->preserveAnimation = $value;

        return $this;
    }

    public function items(array $value = []): \Symfony\Config\Pimcore\Assets\Image\Thumbnails\DefinitionsConfig\ItemsConfig
    {
        $this->_usedProperties['items'] = true;

        return $this->items[] = new \Symfony\Config\Pimcore\Assets\Image\Thumbnails\DefinitionsConfig\ItemsConfig($value);
    }

    public function medias(array $value = []): \Symfony\Config\Pimcore\Assets\Image\Thumbnails\DefinitionsConfig\MediasConfig
    {
        $this->_usedProperties['medias'] = true;

        return $this->medias[] = new \Symfony\Config\Pimcore\Assets\Image\Thumbnails\DefinitionsConfig\MediasConfig($value);
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('id', $value)) {
            $this->_usedProperties['id'] = true;
            $this->id = $value['id'];
            unset($value['id']);
        }

        if (array_key_exists('name', $value)) {
            $this->_usedProperties['name'] = true;
            $this->name = $value['name'];
            unset($value['name']);
        }

        if (array_key_exists('description', $value)) {
            $this->_usedProperties['description'] = true;
            $this->description = $value['description'];
            unset($value['description']);
        }

        if (array_key_exists('group', $value)) {
            $this->_usedProperties['group'] = true;
            $this->group = $value['group'];
            unset($value['group']);
        }

        if (array_key_exists('format', $value)) {
            $this->_usedProperties['format'] = true;
            $this->format = $value['format'];
            unset($value['format']);
        }

        if (array_key_exists('quality', $value)) {
            $this->_usedProperties['quality'] = true;
            $this->quality = $value['quality'];
            unset($value['quality']);
        }

        if (array_key_exists('highResolution', $value)) {
            $this->_usedProperties['highResolution'] = true;
            $this->highResolution = $value['highResolution'];
            unset($value['highResolution']);
        }

        if (array_key_exists('preserveColor', $value)) {
            $this->_usedProperties['preserveColor'] = true;
            $this->preserveColor = $value['preserveColor'];
            unset($value['preserveColor']);
        }

        if (array_key_exists('preserveMetaData', $value)) {
            $this->_usedProperties['preserveMetaData'] = true;
            $this->preserveMetaData = $value['preserveMetaData'];
            unset($value['preserveMetaData']);
        }

        if (array_key_exists('rasterizeSVG', $value)) {
            $this->_usedProperties['rasterizeSVG'] = true;
            $this->rasterizeSVG = $value['rasterizeSVG'];
            unset($value['rasterizeSVG']);
        }

        if (array_key_exists('downloadable', $value)) {
            $this->_usedProperties['downloadable'] = true;
            $this->downloadable = $value['downloadable'];
            unset($value['downloadable']);
        }

        if (array_key_exists('modificationDate', $value)) {
            $this->_usedProperties['modificationDate'] = true;
            $this->modificationDate = $value['modificationDate'];
            unset($value['modificationDate']);
        }

        if (array_key_exists('creationDate', $value)) {
            $this->_usedProperties['creationDate'] = true;
            $this->creationDate = $value['creationDate'];
            unset($value['creationDate']);
        }

        if (array_key_exists('preserveAnimation', $value)) {
            $this->_usedProperties['preserveAnimation'] = true;
            $this->preserveAnimation = $value['preserveAnimation'];
            unset($value['preserveAnimation']);
        }

        if (array_key_exists('items', $value)) {
            $this->_usedProperties['items'] = true;
            $this->items = array_map(fn ($v) => new \Symfony\Config\Pimcore\Assets\Image\Thumbnails\DefinitionsConfig\ItemsConfig($v), $value['items']);
            unset($value['items']);
        }

        if (array_key_exists('medias', $value)) {
            $this->_usedProperties['medias'] = true;
            $this->medias = array_map(fn ($v) => new \Symfony\Config\Pimcore\Assets\Image\Thumbnails\DefinitionsConfig\MediasConfig($v), $value['medias']);
            unset($value['medias']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['id'])) {
            $output['id'] = $this->id;
        }
        if (isset($this->_usedProperties['name'])) {
            $output['name'] = $this->name;
        }
        if (isset($this->_usedProperties['description'])) {
            $output['description'] = $this->description;
        }
        if (isset($this->_usedProperties['group'])) {
            $output['group'] = $this->group;
        }
        if (isset($this->_usedProperties['format'])) {
            $output['format'] = $this->format;
        }
        if (isset($this->_usedProperties['quality'])) {
            $output['quality'] = $this->quality;
        }
        if (isset($this->_usedProperties['highResolution'])) {
            $output['highResolution'] = $this->highResolution;
        }
        if (isset($this->_usedProperties['preserveColor'])) {
            $output['preserveColor'] = $this->preserveColor;
        }
        if (isset($this->_usedProperties['preserveMetaData'])) {
            $output['preserveMetaData'] = $this->preserveMetaData;
        }
        if (isset($this->_usedProperties['rasterizeSVG'])) {
            $output['rasterizeSVG'] = $this->rasterizeSVG;
        }
        if (isset($this->_usedProperties['downloadable'])) {
            $output['downloadable'] = $this->downloadable;
        }
        if (isset($this->_usedProperties['modificationDate'])) {
            $output['modificationDate'] = $this->modificationDate;
        }
        if (isset($this->_usedProperties['creationDate'])) {
            $output['creationDate'] = $this->creationDate;
        }
        if (isset($this->_usedProperties['preserveAnimation'])) {
            $output['preserveAnimation'] = $this->preserveAnimation;
        }
        if (isset($this->_usedProperties['items'])) {
            $output['items'] = array_map(fn ($v) => $v->toArray(), $this->items);
        }
        if (isset($this->_usedProperties['medias'])) {
            $output['medias'] = array_map(fn ($v) => $v->toArray(), $this->medias);
        }

        return $output;
    }

}
