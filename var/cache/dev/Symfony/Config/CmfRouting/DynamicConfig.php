<?php

namespace Symfony\Config\CmfRouting;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Dynamic'.\DIRECTORY_SEPARATOR.'PersistenceConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DynamicConfig 
{
    private $enabled;
    private $routeCollectionLimit;
    private $genericController;
    private $defaultController;
    private $controllersByType;
    private $controllersByClass;
    private $templatesByClass;
    private $persistence;
    private $uriFilterRegexp;
    private $routeProviderServiceId;
    private $routeFiltersById;
    private $contentRepositoryServiceId;
    private $locales;
    private $limitCandidates;
    private $matchImplicitLocale;
    private $redirectableUrlMatcher;
    private $autoLocalePattern;
    private $urlGenerator;
    private $_usedProperties = [];

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function enabled($value): static
    {
        $this->_usedProperties['enabled'] = true;
        $this->enabled = $value;

        return $this;
    }

    /**
     * @default 0
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function routeCollectionLimit($value): static
    {
        $this->_usedProperties['routeCollectionLimit'] = true;
        $this->routeCollectionLimit = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function genericController($value): static
    {
        $this->_usedProperties['genericController'] = true;
        $this->genericController = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function defaultController($value): static
    {
        $this->_usedProperties['defaultController'] = true;
        $this->defaultController = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function controllersByType(string $type, mixed $value): static
    {
        $this->_usedProperties['controllersByType'] = true;
        $this->controllersByType[$type] = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function controllerByClass(string $class, mixed $value): static
    {
        $this->_usedProperties['controllersByClass'] = true;
        $this->controllersByClass[$class] = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function templateByClass(string $class, mixed $value): static
    {
        $this->_usedProperties['templatesByClass'] = true;
        $this->templatesByClass[$class] = $value;

        return $this;
    }

    /**
     * @default {"phpcr":{"enabled":false,"manager_name":null,"route_basepaths":["\/cms\/routes"],"enable_initializer":true},"orm":{"enabled":false,"manager_name":null,"route_class":"Symfony\\Cmf\\Bundle\\RoutingBundle\\Doctrine\\Orm\\Route"}}
    */
    public function persistence(array $value = []): \Symfony\Config\CmfRouting\Dynamic\PersistenceConfig
    {
        if (null === $this->persistence) {
            $this->_usedProperties['persistence'] = true;
            $this->persistence = new \Symfony\Config\CmfRouting\Dynamic\PersistenceConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "persistence()" has already been initialized. You cannot pass values the second time you call persistence().');
        }

        return $this->persistence;
    }

    /**
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function uriFilterRegexp($value): static
    {
        $this->_usedProperties['uriFilterRegexp'] = true;
        $this->uriFilterRegexp = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function routeProviderServiceId($value): static
    {
        $this->_usedProperties['routeProviderServiceId'] = true;
        $this->routeProviderServiceId = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function routeFiltersById(string $id, mixed $value): static
    {
        $this->_usedProperties['routeFiltersById'] = true;
        $this->routeFiltersById[$id] = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function contentRepositoryServiceId($value): static
    {
        $this->_usedProperties['contentRepositoryServiceId'] = true;
        $this->contentRepositoryServiceId = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function locales(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['locales'] = true;
        $this->locales = $value;

        return $this;
    }

    /**
     * @default 20
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function limitCandidates($value): static
    {
        $this->_usedProperties['limitCandidates'] = true;
        $this->limitCandidates = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function matchImplicitLocale($value): static
    {
        $this->_usedProperties['matchImplicitLocale'] = true;
        $this->matchImplicitLocale = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function redirectableUrlMatcher($value): static
    {
        $this->_usedProperties['redirectableUrlMatcher'] = true;
        $this->redirectableUrlMatcher = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function autoLocalePattern($value): static
    {
        $this->_usedProperties['autoLocalePattern'] = true;
        $this->autoLocalePattern = $value;

        return $this;
    }

    /**
     * URL generator service ID
     * @default 'cmf_routing.generator'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function urlGenerator($value): static
    {
        $this->_usedProperties['urlGenerator'] = true;
        $this->urlGenerator = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('enabled', $value)) {
            $this->_usedProperties['enabled'] = true;
            $this->enabled = $value['enabled'];
            unset($value['enabled']);
        }

        if (array_key_exists('route_collection_limit', $value)) {
            $this->_usedProperties['routeCollectionLimit'] = true;
            $this->routeCollectionLimit = $value['route_collection_limit'];
            unset($value['route_collection_limit']);
        }

        if (array_key_exists('generic_controller', $value)) {
            $this->_usedProperties['genericController'] = true;
            $this->genericController = $value['generic_controller'];
            unset($value['generic_controller']);
        }

        if (array_key_exists('default_controller', $value)) {
            $this->_usedProperties['defaultController'] = true;
            $this->defaultController = $value['default_controller'];
            unset($value['default_controller']);
        }

        if (array_key_exists('controllers_by_type', $value)) {
            $this->_usedProperties['controllersByType'] = true;
            $this->controllersByType = $value['controllers_by_type'];
            unset($value['controllers_by_type']);
        }

        if (array_key_exists('controllers_by_class', $value)) {
            $this->_usedProperties['controllersByClass'] = true;
            $this->controllersByClass = $value['controllers_by_class'];
            unset($value['controllers_by_class']);
        }

        if (array_key_exists('templates_by_class', $value)) {
            $this->_usedProperties['templatesByClass'] = true;
            $this->templatesByClass = $value['templates_by_class'];
            unset($value['templates_by_class']);
        }

        if (array_key_exists('persistence', $value)) {
            $this->_usedProperties['persistence'] = true;
            $this->persistence = new \Symfony\Config\CmfRouting\Dynamic\PersistenceConfig($value['persistence']);
            unset($value['persistence']);
        }

        if (array_key_exists('uri_filter_regexp', $value)) {
            $this->_usedProperties['uriFilterRegexp'] = true;
            $this->uriFilterRegexp = $value['uri_filter_regexp'];
            unset($value['uri_filter_regexp']);
        }

        if (array_key_exists('route_provider_service_id', $value)) {
            $this->_usedProperties['routeProviderServiceId'] = true;
            $this->routeProviderServiceId = $value['route_provider_service_id'];
            unset($value['route_provider_service_id']);
        }

        if (array_key_exists('route_filters_by_id', $value)) {
            $this->_usedProperties['routeFiltersById'] = true;
            $this->routeFiltersById = $value['route_filters_by_id'];
            unset($value['route_filters_by_id']);
        }

        if (array_key_exists('content_repository_service_id', $value)) {
            $this->_usedProperties['contentRepositoryServiceId'] = true;
            $this->contentRepositoryServiceId = $value['content_repository_service_id'];
            unset($value['content_repository_service_id']);
        }

        if (array_key_exists('locales', $value)) {
            $this->_usedProperties['locales'] = true;
            $this->locales = $value['locales'];
            unset($value['locales']);
        }

        if (array_key_exists('limit_candidates', $value)) {
            $this->_usedProperties['limitCandidates'] = true;
            $this->limitCandidates = $value['limit_candidates'];
            unset($value['limit_candidates']);
        }

        if (array_key_exists('match_implicit_locale', $value)) {
            $this->_usedProperties['matchImplicitLocale'] = true;
            $this->matchImplicitLocale = $value['match_implicit_locale'];
            unset($value['match_implicit_locale']);
        }

        if (array_key_exists('redirectable_url_matcher', $value)) {
            $this->_usedProperties['redirectableUrlMatcher'] = true;
            $this->redirectableUrlMatcher = $value['redirectable_url_matcher'];
            unset($value['redirectable_url_matcher']);
        }

        if (array_key_exists('auto_locale_pattern', $value)) {
            $this->_usedProperties['autoLocalePattern'] = true;
            $this->autoLocalePattern = $value['auto_locale_pattern'];
            unset($value['auto_locale_pattern']);
        }

        if (array_key_exists('url_generator', $value)) {
            $this->_usedProperties['urlGenerator'] = true;
            $this->urlGenerator = $value['url_generator'];
            unset($value['url_generator']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['enabled'])) {
            $output['enabled'] = $this->enabled;
        }
        if (isset($this->_usedProperties['routeCollectionLimit'])) {
            $output['route_collection_limit'] = $this->routeCollectionLimit;
        }
        if (isset($this->_usedProperties['genericController'])) {
            $output['generic_controller'] = $this->genericController;
        }
        if (isset($this->_usedProperties['defaultController'])) {
            $output['default_controller'] = $this->defaultController;
        }
        if (isset($this->_usedProperties['controllersByType'])) {
            $output['controllers_by_type'] = $this->controllersByType;
        }
        if (isset($this->_usedProperties['controllersByClass'])) {
            $output['controllers_by_class'] = $this->controllersByClass;
        }
        if (isset($this->_usedProperties['templatesByClass'])) {
            $output['templates_by_class'] = $this->templatesByClass;
        }
        if (isset($this->_usedProperties['persistence'])) {
            $output['persistence'] = $this->persistence->toArray();
        }
        if (isset($this->_usedProperties['uriFilterRegexp'])) {
            $output['uri_filter_regexp'] = $this->uriFilterRegexp;
        }
        if (isset($this->_usedProperties['routeProviderServiceId'])) {
            $output['route_provider_service_id'] = $this->routeProviderServiceId;
        }
        if (isset($this->_usedProperties['routeFiltersById'])) {
            $output['route_filters_by_id'] = $this->routeFiltersById;
        }
        if (isset($this->_usedProperties['contentRepositoryServiceId'])) {
            $output['content_repository_service_id'] = $this->contentRepositoryServiceId;
        }
        if (isset($this->_usedProperties['locales'])) {
            $output['locales'] = $this->locales;
        }
        if (isset($this->_usedProperties['limitCandidates'])) {
            $output['limit_candidates'] = $this->limitCandidates;
        }
        if (isset($this->_usedProperties['matchImplicitLocale'])) {
            $output['match_implicit_locale'] = $this->matchImplicitLocale;
        }
        if (isset($this->_usedProperties['redirectableUrlMatcher'])) {
            $output['redirectable_url_matcher'] = $this->redirectableUrlMatcher;
        }
        if (isset($this->_usedProperties['autoLocalePattern'])) {
            $output['auto_locale_pattern'] = $this->autoLocalePattern;
        }
        if (isset($this->_usedProperties['urlGenerator'])) {
            $output['url_generator'] = $this->urlGenerator;
        }

        return $output;
    }

}
