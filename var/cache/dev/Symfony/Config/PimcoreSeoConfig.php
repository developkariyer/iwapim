<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'PimcoreSeo'.\DIRECTORY_SEPARATOR.'SitemapsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'PimcoreSeo'.\DIRECTORY_SEPARATOR.'RedirectsConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class PimcoreSeoConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $sitemaps;
    private $redirects;
    private $_usedProperties = [];

    /**
     * @default {"generators":[]}
    */
    public function sitemaps(array $value = []): \Symfony\Config\PimcoreSeo\SitemapsConfig
    {
        if (null === $this->sitemaps) {
            $this->_usedProperties['sitemaps'] = true;
            $this->sitemaps = new \Symfony\Config\PimcoreSeo\SitemapsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "sitemaps()" has already been initialized. You cannot pass values the second time you call sitemaps().');
        }

        return $this->sitemaps;
    }

    /**
     * @default {"status_codes":[],"auto_create_redirects":false}
    */
    public function redirects(array $value = []): \Symfony\Config\PimcoreSeo\RedirectsConfig
    {
        if (null === $this->redirects) {
            $this->_usedProperties['redirects'] = true;
            $this->redirects = new \Symfony\Config\PimcoreSeo\RedirectsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "redirects()" has already been initialized. You cannot pass values the second time you call redirects().');
        }

        return $this->redirects;
    }

    public function getExtensionAlias(): string
    {
        return 'pimcore_seo';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('sitemaps', $value)) {
            $this->_usedProperties['sitemaps'] = true;
            $this->sitemaps = new \Symfony\Config\PimcoreSeo\SitemapsConfig($value['sitemaps']);
            unset($value['sitemaps']);
        }

        if (array_key_exists('redirects', $value)) {
            $this->_usedProperties['redirects'] = true;
            $this->redirects = new \Symfony\Config\PimcoreSeo\RedirectsConfig($value['redirects']);
            unset($value['redirects']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['sitemaps'])) {
            $output['sitemaps'] = $this->sitemaps->toArray();
        }
        if (isset($this->_usedProperties['redirects'])) {
            $output['redirects'] = $this->redirects->toArray();
        }

        return $output;
    }

}
