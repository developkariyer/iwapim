<?php

namespace Symfony\Config\Pimcore;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class MapsConfig 
{
    private $tileLayerUrlTemplate;
    private $geocodingUrlTemplate;
    private $reverseGeocodingUrlTemplate;
    private $_usedProperties = [];

    /**
     * @default 'https://a.tile.openstreetmap.org/{z}/{x}/{y}.png'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function tileLayerUrlTemplate($value): static
    {
        $this->_usedProperties['tileLayerUrlTemplate'] = true;
        $this->tileLayerUrlTemplate = $value;

        return $this;
    }

    /**
     * @default 'https://nominatim.openstreetmap.org/search?q={q}&addressdetails=1&format=json&limit=1'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function geocodingUrlTemplate($value): static
    {
        $this->_usedProperties['geocodingUrlTemplate'] = true;
        $this->geocodingUrlTemplate = $value;

        return $this;
    }

    /**
     * @default 'https://nominatim.openstreetmap.org/reverse?format=json&lat={lat}&lon={lon}&addressdetails=1'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function reverseGeocodingUrlTemplate($value): static
    {
        $this->_usedProperties['reverseGeocodingUrlTemplate'] = true;
        $this->reverseGeocodingUrlTemplate = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('tile_layer_url_template', $value)) {
            $this->_usedProperties['tileLayerUrlTemplate'] = true;
            $this->tileLayerUrlTemplate = $value['tile_layer_url_template'];
            unset($value['tile_layer_url_template']);
        }

        if (array_key_exists('geocoding_url_template', $value)) {
            $this->_usedProperties['geocodingUrlTemplate'] = true;
            $this->geocodingUrlTemplate = $value['geocoding_url_template'];
            unset($value['geocoding_url_template']);
        }

        if (array_key_exists('reverse_geocoding_url_template', $value)) {
            $this->_usedProperties['reverseGeocodingUrlTemplate'] = true;
            $this->reverseGeocodingUrlTemplate = $value['reverse_geocoding_url_template'];
            unset($value['reverse_geocoding_url_template']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['tileLayerUrlTemplate'])) {
            $output['tile_layer_url_template'] = $this->tileLayerUrlTemplate;
        }
        if (isset($this->_usedProperties['geocodingUrlTemplate'])) {
            $output['geocoding_url_template'] = $this->geocodingUrlTemplate;
        }
        if (isset($this->_usedProperties['reverseGeocodingUrlTemplate'])) {
            $output['reverse_geocoding_url_template'] = $this->reverseGeocodingUrlTemplate;
        }

        return $output;
    }

}
