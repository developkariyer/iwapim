<?php

namespace Symfony\Config\Pimcore\Assets;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Document'.\DIRECTORY_SEPARATOR.'ThumbnailsConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class DocumentConfig 
{
    private $thumbnails;
    private $processPageCount;
    private $processText;
    private $scanPdf;
    private $_usedProperties = [];

    /**
     * @default {"enabled":true}
    */
    public function thumbnails(array $value = []): \Symfony\Config\Pimcore\Assets\Document\ThumbnailsConfig
    {
        if (null === $this->thumbnails) {
            $this->_usedProperties['thumbnails'] = true;
            $this->thumbnails = new \Symfony\Config\Pimcore\Assets\Document\ThumbnailsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "thumbnails()" has already been initialized. You cannot pass values the second time you call thumbnails().');
        }

        return $this->thumbnails;
    }

    /**
     * Process & store page count for Asset documents. Internally required for thumbnails & text generation
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function processPageCount($value): static
    {
        $this->_usedProperties['processPageCount'] = true;
        $this->processPageCount = $value;

        return $this;
    }

    /**
     * Process text for Asset documents (e.g. used by backend search).
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function processText($value): static
    {
        $this->_usedProperties['processText'] = true;
        $this->processText = $value;

        return $this;
    }

    /**
     * Scan PDF documents for unsafe JavaScript.
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function scanPdf($value): static
    {
        $this->_usedProperties['scanPdf'] = true;
        $this->scanPdf = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('thumbnails', $value)) {
            $this->_usedProperties['thumbnails'] = true;
            $this->thumbnails = new \Symfony\Config\Pimcore\Assets\Document\ThumbnailsConfig($value['thumbnails']);
            unset($value['thumbnails']);
        }

        if (array_key_exists('process_page_count', $value)) {
            $this->_usedProperties['processPageCount'] = true;
            $this->processPageCount = $value['process_page_count'];
            unset($value['process_page_count']);
        }

        if (array_key_exists('process_text', $value)) {
            $this->_usedProperties['processText'] = true;
            $this->processText = $value['process_text'];
            unset($value['process_text']);
        }

        if (array_key_exists('scan_pdf', $value)) {
            $this->_usedProperties['scanPdf'] = true;
            $this->scanPdf = $value['scan_pdf'];
            unset($value['scan_pdf']);
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
        if (isset($this->_usedProperties['processPageCount'])) {
            $output['process_page_count'] = $this->processPageCount;
        }
        if (isset($this->_usedProperties['processText'])) {
            $output['process_text'] = $this->processText;
        }
        if (isset($this->_usedProperties['scanPdf'])) {
            $output['scan_pdf'] = $this->scanPdf;
        }

        return $output;
    }

}
