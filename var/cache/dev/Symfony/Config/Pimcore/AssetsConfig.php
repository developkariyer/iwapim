<?php

namespace Symfony\Config\Pimcore;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'ThumbnailsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'FrontendPrefixesConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'ImageConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'VideoConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'DocumentConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'VersionsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'MetadataConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Assets'.\DIRECTORY_SEPARATOR.'TypeDefinitionsConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class AssetsConfig 
{
    private $thumbnails;
    private $frontendPrefixes;
    private $previewImageThumbnail;
    private $defaultUploadPath;
    private $treePagingLimit;
    private $image;
    private $video;
    private $document;
    private $versions;
    private $iccRgbProfile;
    private $iccCmykProfile;
    private $metadata;
    private $typeDefinitions;
    private $_usedProperties = [];

    /**
     * @default {"allowed_formats":["avif","eps","gif","jpeg","jpg","pjpeg","png","svg","tiff","webm","webp","print"],"max_scaling_factor":5}
    */
    public function thumbnails(array $value = []): \Symfony\Config\Pimcore\Assets\ThumbnailsConfig
    {
        if (null === $this->thumbnails) {
            $this->_usedProperties['thumbnails'] = true;
            $this->thumbnails = new \Symfony\Config\Pimcore\Assets\ThumbnailsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "thumbnails()" has already been initialized. You cannot pass values the second time you call thumbnails().');
        }

        return $this->thumbnails;
    }

    /**
     * @default {"source":"","thumbnail":"","thumbnail_deferred":""}
    */
    public function frontendPrefixes(array $value = []): \Symfony\Config\Pimcore\Assets\FrontendPrefixesConfig
    {
        if (null === $this->frontendPrefixes) {
            $this->_usedProperties['frontendPrefixes'] = true;
            $this->frontendPrefixes = new \Symfony\Config\Pimcore\Assets\FrontendPrefixesConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "frontendPrefixes()" has already been initialized. You cannot pass values the second time you call frontendPrefixes().');
        }

        return $this->frontendPrefixes;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function previewImageThumbnail($value): static
    {
        $this->_usedProperties['previewImageThumbnail'] = true;
        $this->previewImageThumbnail = $value;

        return $this;
    }

    /**
     * @default '_default_upload_bucket'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function defaultUploadPath($value): static
    {
        $this->_usedProperties['defaultUploadPath'] = true;
        $this->defaultUploadPath = $value;

        return $this;
    }

    /**
     * @default 100
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function treePagingLimit($value): static
    {
        $this->_usedProperties['treePagingLimit'] = true;
        $this->treePagingLimit = $value;

        return $this;
    }

    /**
     * @default {"max_pixels":40000000,"low_quality_image_preview":{"enabled":true},"thumbnails":{"definitions":[],"clip_auto_support":true,"image_optimizers":{"enabled":true},"auto_formats":{"avif":{"enabled":true,"quality":50},"webp":{"enabled":true,"quality":null}},"status_cache":true,"auto_clear_temp_files":true}}
    */
    public function image(array $value = []): \Symfony\Config\Pimcore\Assets\ImageConfig
    {
        if (null === $this->image) {
            $this->_usedProperties['image'] = true;
            $this->image = new \Symfony\Config\Pimcore\Assets\ImageConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "image()" has already been initialized. You cannot pass values the second time you call image().');
        }

        return $this->image;
    }

    /**
     * @default {"thumbnails":{"definitions":[],"auto_clear_temp_files":true}}
    */
    public function video(array $value = []): \Symfony\Config\Pimcore\Assets\VideoConfig
    {
        if (null === $this->video) {
            $this->_usedProperties['video'] = true;
            $this->video = new \Symfony\Config\Pimcore\Assets\VideoConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "video()" has already been initialized. You cannot pass values the second time you call video().');
        }

        return $this->video;
    }

    /**
     * @default {"thumbnails":{"enabled":true},"process_page_count":true,"process_text":true,"scan_pdf":true}
    */
    public function document(array $value = []): \Symfony\Config\Pimcore\Assets\DocumentConfig
    {
        if (null === $this->document) {
            $this->_usedProperties['document'] = true;
            $this->document = new \Symfony\Config\Pimcore\Assets\DocumentConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "document()" has already been initialized. You cannot pass values the second time you call document().');
        }

        return $this->document;
    }

    /**
     * @default {"days":null,"steps":null,"use_hardlinks":true,"disable_stack_trace":false}
    */
    public function versions(array $value = []): \Symfony\Config\Pimcore\Assets\VersionsConfig
    {
        if (null === $this->versions) {
            $this->_usedProperties['versions'] = true;
            $this->versions = new \Symfony\Config\Pimcore\Assets\VersionsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "versions()" has already been initialized. You cannot pass values the second time you call versions().');
        }

        return $this->versions;
    }

    /**
     * Absolute path to default ICC RGB profile (if no embedded profile is given)
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function iccRgbProfile($value): static
    {
        $this->_usedProperties['iccRgbProfile'] = true;
        $this->iccRgbProfile = $value;

        return $this;
    }

    /**
     * Absolute path to default ICC CMYK profile (if no embedded profile is given)
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function iccCmykProfile($value): static
    {
        $this->_usedProperties['iccCmykProfile'] = true;
        $this->iccCmykProfile = $value;

        return $this;
    }

    /**
     * @default {"predefined":{"definitions":[]}}
    */
    public function metadata(array $value = []): \Symfony\Config\Pimcore\Assets\MetadataConfig
    {
        if (null === $this->metadata) {
            $this->_usedProperties['metadata'] = true;
            $this->metadata = new \Symfony\Config\Pimcore\Assets\MetadataConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "metadata()" has already been initialized. You cannot pass values the second time you call metadata().');
        }

        return $this->metadata;
    }

    /**
     * @default {"map":[]}
    */
    public function typeDefinitions(array $value = []): \Symfony\Config\Pimcore\Assets\TypeDefinitionsConfig
    {
        if (null === $this->typeDefinitions) {
            $this->_usedProperties['typeDefinitions'] = true;
            $this->typeDefinitions = new \Symfony\Config\Pimcore\Assets\TypeDefinitionsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "typeDefinitions()" has already been initialized. You cannot pass values the second time you call typeDefinitions().');
        }

        return $this->typeDefinitions;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('thumbnails', $value)) {
            $this->_usedProperties['thumbnails'] = true;
            $this->thumbnails = new \Symfony\Config\Pimcore\Assets\ThumbnailsConfig($value['thumbnails']);
            unset($value['thumbnails']);
        }

        if (array_key_exists('frontend_prefixes', $value)) {
            $this->_usedProperties['frontendPrefixes'] = true;
            $this->frontendPrefixes = new \Symfony\Config\Pimcore\Assets\FrontendPrefixesConfig($value['frontend_prefixes']);
            unset($value['frontend_prefixes']);
        }

        if (array_key_exists('preview_image_thumbnail', $value)) {
            $this->_usedProperties['previewImageThumbnail'] = true;
            $this->previewImageThumbnail = $value['preview_image_thumbnail'];
            unset($value['preview_image_thumbnail']);
        }

        if (array_key_exists('default_upload_path', $value)) {
            $this->_usedProperties['defaultUploadPath'] = true;
            $this->defaultUploadPath = $value['default_upload_path'];
            unset($value['default_upload_path']);
        }

        if (array_key_exists('tree_paging_limit', $value)) {
            $this->_usedProperties['treePagingLimit'] = true;
            $this->treePagingLimit = $value['tree_paging_limit'];
            unset($value['tree_paging_limit']);
        }

        if (array_key_exists('image', $value)) {
            $this->_usedProperties['image'] = true;
            $this->image = new \Symfony\Config\Pimcore\Assets\ImageConfig($value['image']);
            unset($value['image']);
        }

        if (array_key_exists('video', $value)) {
            $this->_usedProperties['video'] = true;
            $this->video = new \Symfony\Config\Pimcore\Assets\VideoConfig($value['video']);
            unset($value['video']);
        }

        if (array_key_exists('document', $value)) {
            $this->_usedProperties['document'] = true;
            $this->document = new \Symfony\Config\Pimcore\Assets\DocumentConfig($value['document']);
            unset($value['document']);
        }

        if (array_key_exists('versions', $value)) {
            $this->_usedProperties['versions'] = true;
            $this->versions = new \Symfony\Config\Pimcore\Assets\VersionsConfig($value['versions']);
            unset($value['versions']);
        }

        if (array_key_exists('icc_rgb_profile', $value)) {
            $this->_usedProperties['iccRgbProfile'] = true;
            $this->iccRgbProfile = $value['icc_rgb_profile'];
            unset($value['icc_rgb_profile']);
        }

        if (array_key_exists('icc_cmyk_profile', $value)) {
            $this->_usedProperties['iccCmykProfile'] = true;
            $this->iccCmykProfile = $value['icc_cmyk_profile'];
            unset($value['icc_cmyk_profile']);
        }

        if (array_key_exists('metadata', $value)) {
            $this->_usedProperties['metadata'] = true;
            $this->metadata = new \Symfony\Config\Pimcore\Assets\MetadataConfig($value['metadata']);
            unset($value['metadata']);
        }

        if (array_key_exists('type_definitions', $value)) {
            $this->_usedProperties['typeDefinitions'] = true;
            $this->typeDefinitions = new \Symfony\Config\Pimcore\Assets\TypeDefinitionsConfig($value['type_definitions']);
            unset($value['type_definitions']);
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
        if (isset($this->_usedProperties['frontendPrefixes'])) {
            $output['frontend_prefixes'] = $this->frontendPrefixes->toArray();
        }
        if (isset($this->_usedProperties['previewImageThumbnail'])) {
            $output['preview_image_thumbnail'] = $this->previewImageThumbnail;
        }
        if (isset($this->_usedProperties['defaultUploadPath'])) {
            $output['default_upload_path'] = $this->defaultUploadPath;
        }
        if (isset($this->_usedProperties['treePagingLimit'])) {
            $output['tree_paging_limit'] = $this->treePagingLimit;
        }
        if (isset($this->_usedProperties['image'])) {
            $output['image'] = $this->image->toArray();
        }
        if (isset($this->_usedProperties['video'])) {
            $output['video'] = $this->video->toArray();
        }
        if (isset($this->_usedProperties['document'])) {
            $output['document'] = $this->document->toArray();
        }
        if (isset($this->_usedProperties['versions'])) {
            $output['versions'] = $this->versions->toArray();
        }
        if (isset($this->_usedProperties['iccRgbProfile'])) {
            $output['icc_rgb_profile'] = $this->iccRgbProfile;
        }
        if (isset($this->_usedProperties['iccCmykProfile'])) {
            $output['icc_cmyk_profile'] = $this->iccCmykProfile;
        }
        if (isset($this->_usedProperties['metadata'])) {
            $output['metadata'] = $this->metadata->toArray();
        }
        if (isset($this->_usedProperties['typeDefinitions'])) {
            $output['type_definitions'] = $this->typeDefinitions->toArray();
        }

        return $output;
    }

}
