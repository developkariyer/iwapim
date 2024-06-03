<?php

namespace Symfony\Config\Pimcore\Assets;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Video'.\DIRECTORY_SEPARATOR.'ThumbnailsConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class VideoConfig 
{
    private $thumbnails;
    private $_usedProperties = [];

    /**
     * @default {"definitions":[],"auto_clear_temp_files":true}
    */
    public function thumbnails(array $value = []): \Symfony\Config\Pimcore\Assets\Video\ThumbnailsConfig
    {
        if (null === $this->thumbnails) {
            $this->_usedProperties['thumbnails'] = true;
            $this->thumbnails = new \Symfony\Config\Pimcore\Assets\Video\ThumbnailsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "thumbnails()" has already been initialized. You cannot pass values the second time you call thumbnails().');
        }

        return $this->thumbnails;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('thumbnails', $value)) {
            $this->_usedProperties['thumbnails'] = true;
            $this->thumbnails = new \Symfony\Config\Pimcore\Assets\Video\ThumbnailsConfig($value['thumbnails']);
            unset($value['thumbnails']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['thumbnails'])) {
            $output['thumbnails'] = $this->thumbnails->toArray();
        }

        return $output;
    }

}
