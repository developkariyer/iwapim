<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'PrestaSitemap'.\DIRECTORY_SEPARATOR.'DefaultsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'PrestaSitemap'.\DIRECTORY_SEPARATOR.'AlternateConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class PrestaSitemapConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $generator;
    private $dumper;
    private $timetolive;
    private $sitemapFilePrefix;
    private $itemsBySet;
    private $routeAnnotationListener;
    private $dumpDirectory;
    private $defaults;
    private $defaultSection;
    private $alternate;
    private $_usedProperties = [];

    /**
     * @default 'presta_sitemap.generator_default'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function generator($value): static
    {
        $this->_usedProperties['generator'] = true;
        $this->generator = $value;

        return $this;
    }

    /**
     * @default 'presta_sitemap.dumper_default'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function dumper($value): static
    {
        $this->_usedProperties['dumper'] = true;
        $this->dumper = $value;

        return $this;
    }

    /**
     * @default 3600
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function timetolive($value): static
    {
        $this->_usedProperties['timetolive'] = true;
        $this->timetolive = $value;

        return $this;
    }

    /**
     * Sets sitemap filename prefix defaults to "sitemap" -> sitemap.xml (for index); sitemap.<section>.xml(.gz) (for sitemaps)
     * @default 'sitemap'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function sitemapFilePrefix($value): static
    {
        $this->_usedProperties['sitemapFilePrefix'] = true;
        $this->sitemapFilePrefix = $value;

        return $this;
    }

    /**
     * The maximum number of items allowed in single sitemap.
     * @default 50000
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function itemsBySet($value): static
    {
        $this->_usedProperties['itemsBySet'] = true;
        $this->itemsBySet = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function routeAnnotationListener($value): static
    {
        $this->_usedProperties['routeAnnotationListener'] = true;
        $this->routeAnnotationListener = $value;

        return $this;
    }

    /**
     * The directory to which the sitemap will be dumped. It can be either absolute, or relative (to the place where the command will be triggered). Default to Symfony's public dir.
     * @default '%kernel.project_dir%/public'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function dumpDirectory($value): static
    {
        $this->_usedProperties['dumpDirectory'] = true;
        $this->dumpDirectory = $value;

        return $this;
    }

    /**
     * @default {"priority":0.5,"changefreq":"daily","lastmod":"now"}
    */
    public function defaults(array $value = []): \Symfony\Config\PrestaSitemap\DefaultsConfig
    {
        if (null === $this->defaults) {
            $this->_usedProperties['defaults'] = true;
            $this->defaults = new \Symfony\Config\PrestaSitemap\DefaultsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "defaults()" has already been initialized. You cannot pass values the second time you call defaults().');
        }

        return $this->defaults;
    }

    /**
     * The default section in which static routes are registered.
     * @default 'default'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function defaultSection($value): static
    {
        $this->_usedProperties['defaultSection'] = true;
        $this->defaultSection = $value;

        return $this;
    }

    /**
     * @template TValue
     * @param TValue $value
     * Automatically generate alternate (hreflang) urls with static routes. Requires route_annotation_listener config to be enabled.
     * @default {"enabled":false,"default_locale":"en","locales":["en"],"i18n":"symfony"}
     * @return \Symfony\Config\PrestaSitemap\AlternateConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\PrestaSitemap\AlternateConfig : static)
     */
    public function alternate(array $value = []): \Symfony\Config\PrestaSitemap\AlternateConfig|static
    {
        if (!\is_array($value)) {
            $this->_usedProperties['alternate'] = true;
            $this->alternate = $value;

            return $this;
        }

        if (!$this->alternate instanceof \Symfony\Config\PrestaSitemap\AlternateConfig) {
            $this->_usedProperties['alternate'] = true;
            $this->alternate = new \Symfony\Config\PrestaSitemap\AlternateConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "alternate()" has already been initialized. You cannot pass values the second time you call alternate().');
        }

        return $this->alternate;
    }

    public function getExtensionAlias(): string
    {
        return 'presta_sitemap';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('generator', $value)) {
            $this->_usedProperties['generator'] = true;
            $this->generator = $value['generator'];
            unset($value['generator']);
        }

        if (array_key_exists('dumper', $value)) {
            $this->_usedProperties['dumper'] = true;
            $this->dumper = $value['dumper'];
            unset($value['dumper']);
        }

        if (array_key_exists('timetolive', $value)) {
            $this->_usedProperties['timetolive'] = true;
            $this->timetolive = $value['timetolive'];
            unset($value['timetolive']);
        }

        if (array_key_exists('sitemap_file_prefix', $value)) {
            $this->_usedProperties['sitemapFilePrefix'] = true;
            $this->sitemapFilePrefix = $value['sitemap_file_prefix'];
            unset($value['sitemap_file_prefix']);
        }

        if (array_key_exists('items_by_set', $value)) {
            $this->_usedProperties['itemsBySet'] = true;
            $this->itemsBySet = $value['items_by_set'];
            unset($value['items_by_set']);
        }

        if (array_key_exists('route_annotation_listener', $value)) {
            $this->_usedProperties['routeAnnotationListener'] = true;
            $this->routeAnnotationListener = $value['route_annotation_listener'];
            unset($value['route_annotation_listener']);
        }

        if (array_key_exists('dump_directory', $value)) {
            $this->_usedProperties['dumpDirectory'] = true;
            $this->dumpDirectory = $value['dump_directory'];
            unset($value['dump_directory']);
        }

        if (array_key_exists('defaults', $value)) {
            $this->_usedProperties['defaults'] = true;
            $this->defaults = new \Symfony\Config\PrestaSitemap\DefaultsConfig($value['defaults']);
            unset($value['defaults']);
        }

        if (array_key_exists('default_section', $value)) {
            $this->_usedProperties['defaultSection'] = true;
            $this->defaultSection = $value['default_section'];
            unset($value['default_section']);
        }

        if (array_key_exists('alternate', $value)) {
            $this->_usedProperties['alternate'] = true;
            $this->alternate = \is_array($value['alternate']) ? new \Symfony\Config\PrestaSitemap\AlternateConfig($value['alternate']) : $value['alternate'];
            unset($value['alternate']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['generator'])) {
            $output['generator'] = $this->generator;
        }
        if (isset($this->_usedProperties['dumper'])) {
            $output['dumper'] = $this->dumper;
        }
        if (isset($this->_usedProperties['timetolive'])) {
            $output['timetolive'] = $this->timetolive;
        }
        if (isset($this->_usedProperties['sitemapFilePrefix'])) {
            $output['sitemap_file_prefix'] = $this->sitemapFilePrefix;
        }
        if (isset($this->_usedProperties['itemsBySet'])) {
            $output['items_by_set'] = $this->itemsBySet;
        }
        if (isset($this->_usedProperties['routeAnnotationListener'])) {
            $output['route_annotation_listener'] = $this->routeAnnotationListener;
        }
        if (isset($this->_usedProperties['dumpDirectory'])) {
            $output['dump_directory'] = $this->dumpDirectory;
        }
        if (isset($this->_usedProperties['defaults'])) {
            $output['defaults'] = $this->defaults->toArray();
        }
        if (isset($this->_usedProperties['defaultSection'])) {
            $output['default_section'] = $this->defaultSection;
        }
        if (isset($this->_usedProperties['alternate'])) {
            $output['alternate'] = $this->alternate instanceof \Symfony\Config\PrestaSitemap\AlternateConfig ? $this->alternate->toArray() : $this->alternate;
        }

        return $output;
    }

}
