<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'BundlesConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'TranslationsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'MapsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'GeneralConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'MaintenanceConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'ObjectsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'AssetsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'DocumentsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'EncryptionConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'ModelsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'RoutingConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'FullPageCacheConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'ContextConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'WebProfilerConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'SecurityConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'EmailConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'WorkflowsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'HttpclientConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'ApplicationlogConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'PropertiesConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'PerspectivesConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'CustomViewsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'TemplatingEngineConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'GotenbergConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'ChromiumConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Pimcore'.\DIRECTORY_SEPARATOR.'ConfigLocationConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class PimcoreConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $bundles;
    private $flags;
    private $translations;
    private $maps;
    private $general;
    private $maintenance;
    private $objects;
    private $assets;
    private $documents;
    private $encryption;
    private $models;
    private $routing;
    private $fullPageCache;
    private $context;
    private $webProfiler;
    private $security;
    private $email;
    private $workflows;
    private $httpclient;
    private $applicationlog;
    private $properties;
    private $perspectives;
    private $customViews;
    private $templatingEngine;
    private $gotenberg;
    private $chromium;
    private $configLocation;
    private $_usedProperties = [];

    /**
     * @default {"search_paths":[],"handle_composer":true}
    */
    public function bundles(array $value = []): \Symfony\Config\Pimcore\BundlesConfig
    {
        if (null === $this->bundles) {
            $this->_usedProperties['bundles'] = true;
            $this->bundles = new \Symfony\Config\Pimcore\BundlesConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "bundles()" has already been initialized. You cannot pass values the second time you call bundles().');
        }

        return $this->bundles;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function flags(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['flags'] = true;
        $this->flags = $value;

        return $this;
    }

    /**
     * @default {"domains":[],"admin_translation_mapping":[],"debugging":{"enabled":true,"parameter":"pimcore_debug_translations"}}
    */
    public function translations(array $value = []): \Symfony\Config\Pimcore\TranslationsConfig
    {
        if (null === $this->translations) {
            $this->_usedProperties['translations'] = true;
            $this->translations = new \Symfony\Config\Pimcore\TranslationsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "translations()" has already been initialized. You cannot pass values the second time you call translations().');
        }

        return $this->translations;
    }

    /**
     * @default {"tile_layer_url_template":"https:\/\/a.tile.openstreetmap.org\/{z}\/{x}\/{y}.png","geocoding_url_template":"https:\/\/nominatim.openstreetmap.org\/search?q={q}&addressdetails=1&format=json&limit=1","reverse_geocoding_url_template":"https:\/\/nominatim.openstreetmap.org\/reverse?format=json&lat={lat}&lon={lon}&addressdetails=1"}
    */
    public function maps(array $value = []): \Symfony\Config\Pimcore\MapsConfig
    {
        if (null === $this->maps) {
            $this->_usedProperties['maps'] = true;
            $this->maps = new \Symfony\Config\Pimcore\MapsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "maps()" has already been initialized. You cannot pass values the second time you call maps().');
        }

        return $this->maps;
    }

    /**
     * @default {"timezone":"","path_variable":null,"domain":"","redirect_to_maindomain":false,"language":"en","valid_languages":["en","de","fr"],"fallback_languages":[],"default_language":"en","disable_usage_statistics":false,"debug_admin_translations":false}
    */
    public function general(array $value = []): \Symfony\Config\Pimcore\GeneralConfig
    {
        if (null === $this->general) {
            $this->_usedProperties['general'] = true;
            $this->general = new \Symfony\Config\Pimcore\GeneralConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "general()" has already been initialized. You cannot pass values the second time you call general().');
        }

        return $this->general;
    }

    /**
     * @default {"housekeeping":{"cleanup_tmp_files_atime_older_than":7776000,"cleanup_profiler_files_atime_older_than":1800}}
    */
    public function maintenance(array $value = []): \Symfony\Config\Pimcore\MaintenanceConfig
    {
        if (null === $this->maintenance) {
            $this->_usedProperties['maintenance'] = true;
            $this->maintenance = new \Symfony\Config\Pimcore\MaintenanceConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "maintenance()" has already been initialized. You cannot pass values the second time you call maintenance().');
        }

        return $this->maintenance;
    }

    /**
     * @default {"ignore_localized_query_fallback":false,"tree_paging_limit":30,"auto_save_interval":60,"custom_layouts":{"definitions":[]},"select_options":{"definitions":[]},"class_definitions":{"data":{"map":[],"prefixes":[]},"layout":{"map":[],"prefixes":[]}}}
    */
    public function objects(array $value = []): \Symfony\Config\Pimcore\ObjectsConfig
    {
        if (null === $this->objects) {
            $this->_usedProperties['objects'] = true;
            $this->objects = new \Symfony\Config\Pimcore\ObjectsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "objects()" has already been initialized. You cannot pass values the second time you call objects().');
        }

        return $this->objects;
    }

    /**
     * @default {"frontend_prefixes":{"source":"","thumbnail":"","thumbnail_deferred":""},"preview_image_thumbnail":null,"default_upload_path":"_default_upload_bucket","tree_paging_limit":100,"image":{"max_pixels":40000000,"low_quality_image_preview":{"enabled":true},"thumbnails":{"definitions":[],"clip_auto_support":true,"image_optimizers":{"enabled":true},"auto_formats":{"avif":{"enabled":true,"quality":50},"webp":{"enabled":true,"quality":null}},"status_cache":true,"auto_clear_temp_files":true}},"video":{"thumbnails":{"definitions":[],"auto_clear_temp_files":true}},"document":{"thumbnails":{"enabled":true},"process_page_count":true,"process_text":true,"scan_pdf":true},"versions":{"days":null,"steps":null,"use_hardlinks":true,"disable_stack_trace":false},"icc_rgb_profile":null,"icc_cmyk_profile":null,"metadata":{"predefined":{"definitions":[]}},"type_definitions":{"map":[]}}
    */
    public function assets(array $value = []): \Symfony\Config\Pimcore\AssetsConfig
    {
        if (null === $this->assets) {
            $this->_usedProperties['assets'] = true;
            $this->assets = new \Symfony\Config\Pimcore\AssetsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "assets()" has already been initialized. You cannot pass values the second time you call assets().');
        }

        return $this->assets;
    }

    /**
     * @default {"doc_types":{"definitions":[]},"default_controller":"App\\Controller\\DefaultController::defaultAction","allow_trailing_slash":"no","generate_preview":false,"preview_url_prefix":"","tree_paging_limit":50,"editables":{"map":[],"prefixes":[]},"areas":{"autoload":true},"auto_save_interval":60,"static_page_router":{"enabled":false,"route_pattern":null},"static_page_generator":{"use_main_domain":false,"headers":[]},"type_definitions":{"map":[]}}
    */
    public function documents(array $value = []): \Symfony\Config\Pimcore\DocumentsConfig
    {
        if (null === $this->documents) {
            $this->_usedProperties['documents'] = true;
            $this->documents = new \Symfony\Config\Pimcore\DocumentsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "documents()" has already been initialized. You cannot pass values the second time you call documents().');
        }

        return $this->documents;
    }

    /**
     * @default {"secret":null}
    */
    public function encryption(array $value = []): \Symfony\Config\Pimcore\EncryptionConfig
    {
        if (null === $this->encryption) {
            $this->_usedProperties['encryption'] = true;
            $this->encryption = new \Symfony\Config\Pimcore\EncryptionConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "encryption()" has already been initialized. You cannot pass values the second time you call encryption().');
        }

        return $this->encryption;
    }

    /**
     * @default {"class_overrides":[]}
    */
    public function models(array $value = []): \Symfony\Config\Pimcore\ModelsConfig
    {
        if (null === $this->models) {
            $this->_usedProperties['models'] = true;
            $this->models = new \Symfony\Config\Pimcore\ModelsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "models()" has already been initialized. You cannot pass values the second time you call models().');
        }

        return $this->models;
    }

    /**
     * @default {"static":{"locale_params":[]}}
    */
    public function routing(array $value = []): \Symfony\Config\Pimcore\RoutingConfig
    {
        if (null === $this->routing) {
            $this->_usedProperties['routing'] = true;
            $this->routing = new \Symfony\Config\Pimcore\RoutingConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "routing()" has already been initialized. You cannot pass values the second time you call routing().');
        }

        return $this->routing;
    }

    /**
     * @default {"enabled":true,"lifetime":null}
    */
    public function fullPageCache(array $value = []): \Symfony\Config\Pimcore\FullPageCacheConfig
    {
        if (null === $this->fullPageCache) {
            $this->_usedProperties['fullPageCache'] = true;
            $this->fullPageCache = new \Symfony\Config\Pimcore\FullPageCacheConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "fullPageCache()" has already been initialized. You cannot pass values the second time you call fullPageCache().');
        }

        return $this->fullPageCache;
    }

    public function context(string $name, array $value = []): \Symfony\Config\Pimcore\ContextConfig
    {
        if (!isset($this->context[$name])) {
            $this->_usedProperties['context'] = true;
            $this->context[$name] = new \Symfony\Config\Pimcore\ContextConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "context()" has already been initialized. You cannot pass values the second time you call context().');
        }

        return $this->context[$name];
    }

    /**
     * @example {"excluded_routes":[{"path":"^\/test\/path"}]}
     * @default {"toolbar":{"excluded_routes":[]}}
    */
    public function webProfiler(array $value = []): \Symfony\Config\Pimcore\WebProfilerConfig
    {
        if (null === $this->webProfiler) {
            $this->_usedProperties['webProfiler'] = true;
            $this->webProfiler = new \Symfony\Config\Pimcore\WebProfilerConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "webProfiler()" has already been initialized. You cannot pass values the second time you call webProfiler().');
        }

        return $this->webProfiler;
    }

    /**
     * @default {"password":{"algorithm":"2y","options":[]},"factory_type":"encoder","encoder_factories":[],"password_hasher_factories":[]}
    */
    public function security(array $value = []): \Symfony\Config\Pimcore\SecurityConfig
    {
        if (null === $this->security) {
            $this->_usedProperties['security'] = true;
            $this->security = new \Symfony\Config\Pimcore\SecurityConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "security()" has already been initialized. You cannot pass values the second time you call security().');
        }

        return $this->security;
    }

    /**
     * @default {"sender":{"name":"","email":""},"return":{"name":"","email":""},"debug":{"email_addresses":""},"usespecific":false}
    */
    public function email(array $value = []): \Symfony\Config\Pimcore\EmailConfig
    {
        if (null === $this->email) {
            $this->_usedProperties['email'] = true;
            $this->email = new \Symfony\Config\Pimcore\EmailConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "email()" has already been initialized. You cannot pass values the second time you call email().');
        }

        return $this->email;
    }

    public function workflows(string $name, array $value = []): \Symfony\Config\Pimcore\WorkflowsConfig
    {
        if (!isset($this->workflows[$name])) {
            $this->_usedProperties['workflows'] = true;
            $this->workflows[$name] = new \Symfony\Config\Pimcore\WorkflowsConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "workflows()" has already been initialized. You cannot pass values the second time you call workflows().');
        }

        return $this->workflows[$name];
    }

    /**
     * @default {"adapter":"Socket","proxy_host":null,"proxy_port":null,"proxy_user":null,"proxy_pass":null}
    */
    public function httpclient(array $value = []): \Symfony\Config\Pimcore\HttpclientConfig
    {
        if (null === $this->httpclient) {
            $this->_usedProperties['httpclient'] = true;
            $this->httpclient = new \Symfony\Config\Pimcore\HttpclientConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "httpclient()" has already been initialized. You cannot pass values the second time you call httpclient().');
        }

        return $this->httpclient;
    }

    /**
     * @default {"archive_treshold":30,"archive_alternative_database":"","delete_archive_threshold":"6"}
    */
    public function applicationlog(array $value = []): \Symfony\Config\Pimcore\ApplicationlogConfig
    {
        if (null === $this->applicationlog) {
            $this->_usedProperties['applicationlog'] = true;
            $this->applicationlog = new \Symfony\Config\Pimcore\ApplicationlogConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "applicationlog()" has already been initialized. You cannot pass values the second time you call applicationlog().');
        }

        return $this->applicationlog;
    }

    /**
     * @default {"predefined":{"definitions":[]}}
    */
    public function properties(array $value = []): \Symfony\Config\Pimcore\PropertiesConfig
    {
        if (null === $this->properties) {
            $this->_usedProperties['properties'] = true;
            $this->properties = new \Symfony\Config\Pimcore\PropertiesConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "properties()" has already been initialized. You cannot pass values the second time you call properties().');
        }

        return $this->properties;
    }

    /**
     * @default {"definitions":[]}
    */
    public function perspectives(array $value = []): \Symfony\Config\Pimcore\PerspectivesConfig
    {
        if (null === $this->perspectives) {
            $this->_usedProperties['perspectives'] = true;
            $this->perspectives = new \Symfony\Config\Pimcore\PerspectivesConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "perspectives()" has already been initialized. You cannot pass values the second time you call perspectives().');
        }

        return $this->perspectives;
    }

    /**
     * @default {"definitions":[]}
    */
    public function customViews(array $value = []): \Symfony\Config\Pimcore\CustomViewsConfig
    {
        if (null === $this->customViews) {
            $this->_usedProperties['customViews'] = true;
            $this->customViews = new \Symfony\Config\Pimcore\CustomViewsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "customViews()" has already been initialized. You cannot pass values the second time you call customViews().');
        }

        return $this->customViews;
    }

    /**
     * @default {"twig":[]}
    */
    public function templatingEngine(array $value = []): \Symfony\Config\Pimcore\TemplatingEngineConfig
    {
        if (null === $this->templatingEngine) {
            $this->_usedProperties['templatingEngine'] = true;
            $this->templatingEngine = new \Symfony\Config\Pimcore\TemplatingEngineConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "templatingEngine()" has already been initialized. You cannot pass values the second time you call templatingEngine().');
        }

        return $this->templatingEngine;
    }

    /**
     * @default {"base_url":"http:\/\/gotenberg:3000"}
    */
    public function gotenberg(array $value = []): \Symfony\Config\Pimcore\GotenbergConfig
    {
        if (null === $this->gotenberg) {
            $this->_usedProperties['gotenberg'] = true;
            $this->gotenberg = new \Symfony\Config\Pimcore\GotenbergConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "gotenberg()" has already been initialized. You cannot pass values the second time you call gotenberg().');
        }

        return $this->gotenberg;
    }

    /**
     * @default {"uri":null}
     * @deprecated Chromium service is deprecated and will be removed in Pimcore 12. Use Gotenberg instead.
    */
    public function chromium(array $value = []): \Symfony\Config\Pimcore\ChromiumConfig
    {
        if (null === $this->chromium) {
            $this->_usedProperties['chromium'] = true;
            $this->chromium = new \Symfony\Config\Pimcore\ChromiumConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "chromium()" has already been initialized. You cannot pass values the second time you call chromium().');
        }

        return $this->chromium;
    }

    /**
     * @default {"image_thumbnails":{"write_target":{"type":"symfony-config","options":{"directory":"\/var\/www\/iwapim\/var\/config\/image_thumbnails"}}},"video_thumbnails":{"write_target":{"type":"symfony-config","options":{"directory":"\/var\/www\/iwapim\/var\/config\/video_thumbnails"}}},"document_types":{"write_target":{"type":"symfony-config","options":{"directory":"\/var\/www\/iwapim\/var\/config\/document_types"}}},"predefined_properties":{"write_target":{"type":"symfony-config","options":{"directory":"\/var\/www\/iwapim\/var\/config\/predefined_properties"}}},"predefined_asset_metadata":{"write_target":{"type":"symfony-config","options":{"directory":"\/var\/www\/iwapim\/var\/config\/predefined_asset_metadata"}}},"perspectives":{"write_target":{"type":"symfony-config","options":{"directory":"\/var\/www\/iwapim\/var\/config\/perspectives"}}},"custom_views":{"write_target":{"type":"symfony-config","options":{"directory":"\/var\/www\/iwapim\/var\/config\/custom_views"}}},"object_custom_layouts":{"write_target":{"type":"symfony-config","options":{"directory":"\/var\/www\/iwapim\/var\/config\/object_custom_layouts"}}},"system_settings":{"write_target":{"type":"symfony-config","options":{"directory":"\/var\/www\/iwapim\/var\/config\/system_settings"}},"read_target":{"type":null,"options":{"directory":null}}},"select_options":{"write_target":{"type":"symfony-config","options":{"directory":"\/var\/www\/iwapim\/var\/config\/select_options"}},"read_target":{"type":null,"options":{"directory":null}}}}
    */
    public function configLocation(array $value = []): \Symfony\Config\Pimcore\ConfigLocationConfig
    {
        if (null === $this->configLocation) {
            $this->_usedProperties['configLocation'] = true;
            $this->configLocation = new \Symfony\Config\Pimcore\ConfigLocationConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "configLocation()" has already been initialized. You cannot pass values the second time you call configLocation().');
        }

        return $this->configLocation;
    }

    public function getExtensionAlias(): string
    {
        return 'pimcore';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('bundles', $value)) {
            $this->_usedProperties['bundles'] = true;
            $this->bundles = new \Symfony\Config\Pimcore\BundlesConfig($value['bundles']);
            unset($value['bundles']);
        }

        if (array_key_exists('flags', $value)) {
            $this->_usedProperties['flags'] = true;
            $this->flags = $value['flags'];
            unset($value['flags']);
        }

        if (array_key_exists('translations', $value)) {
            $this->_usedProperties['translations'] = true;
            $this->translations = new \Symfony\Config\Pimcore\TranslationsConfig($value['translations']);
            unset($value['translations']);
        }

        if (array_key_exists('maps', $value)) {
            $this->_usedProperties['maps'] = true;
            $this->maps = new \Symfony\Config\Pimcore\MapsConfig($value['maps']);
            unset($value['maps']);
        }

        if (array_key_exists('general', $value)) {
            $this->_usedProperties['general'] = true;
            $this->general = new \Symfony\Config\Pimcore\GeneralConfig($value['general']);
            unset($value['general']);
        }

        if (array_key_exists('maintenance', $value)) {
            $this->_usedProperties['maintenance'] = true;
            $this->maintenance = new \Symfony\Config\Pimcore\MaintenanceConfig($value['maintenance']);
            unset($value['maintenance']);
        }

        if (array_key_exists('objects', $value)) {
            $this->_usedProperties['objects'] = true;
            $this->objects = new \Symfony\Config\Pimcore\ObjectsConfig($value['objects']);
            unset($value['objects']);
        }

        if (array_key_exists('assets', $value)) {
            $this->_usedProperties['assets'] = true;
            $this->assets = new \Symfony\Config\Pimcore\AssetsConfig($value['assets']);
            unset($value['assets']);
        }

        if (array_key_exists('documents', $value)) {
            $this->_usedProperties['documents'] = true;
            $this->documents = new \Symfony\Config\Pimcore\DocumentsConfig($value['documents']);
            unset($value['documents']);
        }

        if (array_key_exists('encryption', $value)) {
            $this->_usedProperties['encryption'] = true;
            $this->encryption = new \Symfony\Config\Pimcore\EncryptionConfig($value['encryption']);
            unset($value['encryption']);
        }

        if (array_key_exists('models', $value)) {
            $this->_usedProperties['models'] = true;
            $this->models = new \Symfony\Config\Pimcore\ModelsConfig($value['models']);
            unset($value['models']);
        }

        if (array_key_exists('routing', $value)) {
            $this->_usedProperties['routing'] = true;
            $this->routing = new \Symfony\Config\Pimcore\RoutingConfig($value['routing']);
            unset($value['routing']);
        }

        if (array_key_exists('full_page_cache', $value)) {
            $this->_usedProperties['fullPageCache'] = true;
            $this->fullPageCache = new \Symfony\Config\Pimcore\FullPageCacheConfig($value['full_page_cache']);
            unset($value['full_page_cache']);
        }

        if (array_key_exists('context', $value)) {
            $this->_usedProperties['context'] = true;
            $this->context = array_map(fn ($v) => new \Symfony\Config\Pimcore\ContextConfig($v), $value['context']);
            unset($value['context']);
        }

        if (array_key_exists('web_profiler', $value)) {
            $this->_usedProperties['webProfiler'] = true;
            $this->webProfiler = new \Symfony\Config\Pimcore\WebProfilerConfig($value['web_profiler']);
            unset($value['web_profiler']);
        }

        if (array_key_exists('security', $value)) {
            $this->_usedProperties['security'] = true;
            $this->security = new \Symfony\Config\Pimcore\SecurityConfig($value['security']);
            unset($value['security']);
        }

        if (array_key_exists('email', $value)) {
            $this->_usedProperties['email'] = true;
            $this->email = new \Symfony\Config\Pimcore\EmailConfig($value['email']);
            unset($value['email']);
        }

        if (array_key_exists('workflows', $value)) {
            $this->_usedProperties['workflows'] = true;
            $this->workflows = array_map(fn ($v) => new \Symfony\Config\Pimcore\WorkflowsConfig($v), $value['workflows']);
            unset($value['workflows']);
        }

        if (array_key_exists('httpclient', $value)) {
            $this->_usedProperties['httpclient'] = true;
            $this->httpclient = new \Symfony\Config\Pimcore\HttpclientConfig($value['httpclient']);
            unset($value['httpclient']);
        }

        if (array_key_exists('applicationlog', $value)) {
            $this->_usedProperties['applicationlog'] = true;
            $this->applicationlog = new \Symfony\Config\Pimcore\ApplicationlogConfig($value['applicationlog']);
            unset($value['applicationlog']);
        }

        if (array_key_exists('properties', $value)) {
            $this->_usedProperties['properties'] = true;
            $this->properties = new \Symfony\Config\Pimcore\PropertiesConfig($value['properties']);
            unset($value['properties']);
        }

        if (array_key_exists('perspectives', $value)) {
            $this->_usedProperties['perspectives'] = true;
            $this->perspectives = new \Symfony\Config\Pimcore\PerspectivesConfig($value['perspectives']);
            unset($value['perspectives']);
        }

        if (array_key_exists('custom_views', $value)) {
            $this->_usedProperties['customViews'] = true;
            $this->customViews = new \Symfony\Config\Pimcore\CustomViewsConfig($value['custom_views']);
            unset($value['custom_views']);
        }

        if (array_key_exists('templating_engine', $value)) {
            $this->_usedProperties['templatingEngine'] = true;
            $this->templatingEngine = new \Symfony\Config\Pimcore\TemplatingEngineConfig($value['templating_engine']);
            unset($value['templating_engine']);
        }

        if (array_key_exists('gotenberg', $value)) {
            $this->_usedProperties['gotenberg'] = true;
            $this->gotenberg = new \Symfony\Config\Pimcore\GotenbergConfig($value['gotenberg']);
            unset($value['gotenberg']);
        }

        if (array_key_exists('chromium', $value)) {
            $this->_usedProperties['chromium'] = true;
            $this->chromium = new \Symfony\Config\Pimcore\ChromiumConfig($value['chromium']);
            unset($value['chromium']);
        }

        if (array_key_exists('config_location', $value)) {
            $this->_usedProperties['configLocation'] = true;
            $this->configLocation = new \Symfony\Config\Pimcore\ConfigLocationConfig($value['config_location']);
            unset($value['config_location']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['bundles'])) {
            $output['bundles'] = $this->bundles->toArray();
        }
        if (isset($this->_usedProperties['flags'])) {
            $output['flags'] = $this->flags;
        }
        if (isset($this->_usedProperties['translations'])) {
            $output['translations'] = $this->translations->toArray();
        }
        if (isset($this->_usedProperties['maps'])) {
            $output['maps'] = $this->maps->toArray();
        }
        if (isset($this->_usedProperties['general'])) {
            $output['general'] = $this->general->toArray();
        }
        if (isset($this->_usedProperties['maintenance'])) {
            $output['maintenance'] = $this->maintenance->toArray();
        }
        if (isset($this->_usedProperties['objects'])) {
            $output['objects'] = $this->objects->toArray();
        }
        if (isset($this->_usedProperties['assets'])) {
            $output['assets'] = $this->assets->toArray();
        }
        if (isset($this->_usedProperties['documents'])) {
            $output['documents'] = $this->documents->toArray();
        }
        if (isset($this->_usedProperties['encryption'])) {
            $output['encryption'] = $this->encryption->toArray();
        }
        if (isset($this->_usedProperties['models'])) {
            $output['models'] = $this->models->toArray();
        }
        if (isset($this->_usedProperties['routing'])) {
            $output['routing'] = $this->routing->toArray();
        }
        if (isset($this->_usedProperties['fullPageCache'])) {
            $output['full_page_cache'] = $this->fullPageCache->toArray();
        }
        if (isset($this->_usedProperties['context'])) {
            $output['context'] = array_map(fn ($v) => $v->toArray(), $this->context);
        }
        if (isset($this->_usedProperties['webProfiler'])) {
            $output['web_profiler'] = $this->webProfiler->toArray();
        }
        if (isset($this->_usedProperties['security'])) {
            $output['security'] = $this->security->toArray();
        }
        if (isset($this->_usedProperties['email'])) {
            $output['email'] = $this->email->toArray();
        }
        if (isset($this->_usedProperties['workflows'])) {
            $output['workflows'] = array_map(fn ($v) => $v->toArray(), $this->workflows);
        }
        if (isset($this->_usedProperties['httpclient'])) {
            $output['httpclient'] = $this->httpclient->toArray();
        }
        if (isset($this->_usedProperties['applicationlog'])) {
            $output['applicationlog'] = $this->applicationlog->toArray();
        }
        if (isset($this->_usedProperties['properties'])) {
            $output['properties'] = $this->properties->toArray();
        }
        if (isset($this->_usedProperties['perspectives'])) {
            $output['perspectives'] = $this->perspectives->toArray();
        }
        if (isset($this->_usedProperties['customViews'])) {
            $output['custom_views'] = $this->customViews->toArray();
        }
        if (isset($this->_usedProperties['templatingEngine'])) {
            $output['templating_engine'] = $this->templatingEngine->toArray();
        }
        if (isset($this->_usedProperties['gotenberg'])) {
            $output['gotenberg'] = $this->gotenberg->toArray();
        }
        if (isset($this->_usedProperties['chromium'])) {
            $output['chromium'] = $this->chromium->toArray();
        }
        if (isset($this->_usedProperties['configLocation'])) {
            $output['config_location'] = $this->configLocation->toArray();
        }

        return $output;
    }

}
