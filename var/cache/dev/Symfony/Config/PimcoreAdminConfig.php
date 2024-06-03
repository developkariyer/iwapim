<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'PimcoreAdmin'.\DIRECTORY_SEPARATOR.'GdprDataExtractorConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'PimcoreAdmin'.\DIRECTORY_SEPARATOR.'ObjectsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'PimcoreAdmin'.\DIRECTORY_SEPARATOR.'AssetsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'PimcoreAdmin'.\DIRECTORY_SEPARATOR.'DocumentsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'PimcoreAdmin'.\DIRECTORY_SEPARATOR.'NotificationsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'PimcoreAdmin'.\DIRECTORY_SEPARATOR.'UserConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'PimcoreAdmin'.\DIRECTORY_SEPARATOR.'CsrfProtectionConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'PimcoreAdmin'.\DIRECTORY_SEPARATOR.'AdminCspHeaderConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'PimcoreAdmin'.\DIRECTORY_SEPARATOR.'BrandingConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'PimcoreAdmin'.\DIRECTORY_SEPARATOR.'SessionConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'PimcoreAdmin'.\DIRECTORY_SEPARATOR.'TranslationsConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'PimcoreAdmin'.\DIRECTORY_SEPARATOR.'ConfigLocationConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class PimcoreAdminConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $gdprDataExtractor;
    private $objects;
    private $assets;
    private $documents;
    private $notifications;
    private $user;
    private $adminLanguages;
    private $csrfProtection;
    private $adminCspHeader;
    private $customAdminPathIdentifier;
    private $customAdminRouteName;
    private $branding;
    private $session;
    private $translations;
    private $securityFirewall;
    private $configLocation;
    private $_usedProperties = [];

    /**
     * @default {"dataObjects":{"classes":[]},"assets":{"types":[]}}
    */
    public function gdprDataExtractor(array $value = []): \Symfony\Config\PimcoreAdmin\GdprDataExtractorConfig
    {
        if (null === $this->gdprDataExtractor) {
            $this->_usedProperties['gdprDataExtractor'] = true;
            $this->gdprDataExtractor = new \Symfony\Config\PimcoreAdmin\GdprDataExtractorConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "gdprDataExtractor()" has already been initialized. You cannot pass values the second time you call gdprDataExtractor().');
        }

        return $this->gdprDataExtractor;
    }

    /**
     * @default {"notes_events":{"types":["","content","seo","warning","notice"]}}
    */
    public function objects(array $value = []): \Symfony\Config\PimcoreAdmin\ObjectsConfig
    {
        if (null === $this->objects) {
            $this->_usedProperties['objects'] = true;
            $this->objects = new \Symfony\Config\PimcoreAdmin\ObjectsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "objects()" has already been initialized. You cannot pass values the second time you call objects().');
        }

        return $this->objects;
    }

    /**
     * @default {"notes_events":{"types":["","content","seo","warning","notice"]},"hide_edit_image":false,"disable_tree_preview":true}
    */
    public function assets(array $value = []): \Symfony\Config\PimcoreAdmin\AssetsConfig
    {
        if (null === $this->assets) {
            $this->_usedProperties['assets'] = true;
            $this->assets = new \Symfony\Config\PimcoreAdmin\AssetsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "assets()" has already been initialized. You cannot pass values the second time you call assets().');
        }

        return $this->assets;
    }

    /**
     * @default {"notes_events":{"types":["","content","seo","warning","notice"]},"email_search":[]}
    */
    public function documents(array $value = []): \Symfony\Config\PimcoreAdmin\DocumentsConfig
    {
        if (null === $this->documents) {
            $this->_usedProperties['documents'] = true;
            $this->documents = new \Symfony\Config\PimcoreAdmin\DocumentsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "documents()" has already been initialized. You cannot pass values the second time you call documents().');
        }

        return $this->documents;
    }

    /**
     * @default {"enabled":true,"check_new_notification":{"enabled":true,"interval":30}}
    */
    public function notifications(array $value = []): \Symfony\Config\PimcoreAdmin\NotificationsConfig
    {
        if (null === $this->notifications) {
            $this->_usedProperties['notifications'] = true;
            $this->notifications = new \Symfony\Config\PimcoreAdmin\NotificationsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "notifications()" has already been initialized. You cannot pass values the second time you call notifications().');
        }

        return $this->notifications;
    }

    /**
     * @default {"default_key_bindings":[]}
    */
    public function user(array $value = []): \Symfony\Config\PimcoreAdmin\UserConfig
    {
        if (null === $this->user) {
            $this->_usedProperties['user'] = true;
            $this->user = new \Symfony\Config\PimcoreAdmin\UserConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "user()" has already been initialized. You cannot pass values the second time you call user().');
        }

        return $this->user;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function adminLanguages(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['adminLanguages'] = true;
        $this->adminLanguages = $value;

        return $this;
    }

    /**
     * @default {"excluded_routes":[]}
    */
    public function csrfProtection(array $value = []): \Symfony\Config\PimcoreAdmin\CsrfProtectionConfig
    {
        if (null === $this->csrfProtection) {
            $this->_usedProperties['csrfProtection'] = true;
            $this->csrfProtection = new \Symfony\Config\PimcoreAdmin\CsrfProtectionConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "csrfProtection()" has already been initialized. You cannot pass values the second time you call csrfProtection().');
        }

        return $this->csrfProtection;
    }

    /**
     * Can be used to enable or disable the Content Security Policy headers.
     * @default {"enabled":true,"exclude_paths":[],"additional_urls":{"default-src":[],"img-src":[],"script-src":[],"style-src":[],"connect-src":[],"font-src":[],"media-src":[],"frame-src":[]}}
    */
    public function adminCspHeader(array $value = []): \Symfony\Config\PimcoreAdmin\AdminCspHeaderConfig
    {
        if (null === $this->adminCspHeader) {
            $this->_usedProperties['adminCspHeader'] = true;
            $this->adminCspHeader = new \Symfony\Config\PimcoreAdmin\AdminCspHeaderConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "adminCspHeader()" has already been initialized. You cannot pass values the second time you call adminCspHeader().');
        }

        return $this->adminCspHeader;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function customAdminPathIdentifier($value): static
    {
        $this->_usedProperties['customAdminPathIdentifier'] = true;
        $this->customAdminPathIdentifier = $value;

        return $this;
    }

    /**
     * @default 'my_custom_admin_entry_point'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function customAdminRouteName($value): static
    {
        $this->_usedProperties['customAdminRouteName'] = true;
        $this->customAdminRouteName = $value;

        return $this;
    }

    /**
     * @default {"login_screen_invert_colors":false,"color_login_screen":null,"color_admin_interface":null,"color_admin_interface_background":null,"login_screen_custom_image":""}
    */
    public function branding(array $value = []): \Symfony\Config\PimcoreAdmin\BrandingConfig
    {
        if (null === $this->branding) {
            $this->_usedProperties['branding'] = true;
            $this->branding = new \Symfony\Config\PimcoreAdmin\BrandingConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "branding()" has already been initialized. You cannot pass values the second time you call branding().');
        }

        return $this->branding;
    }

    /**
     * @default {"attribute_bags":[]}
    */
    public function session(array $value = []): \Symfony\Config\PimcoreAdmin\SessionConfig
    {
        if (null === $this->session) {
            $this->_usedProperties['session'] = true;
            $this->session = new \Symfony\Config\PimcoreAdmin\SessionConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "session()" has already been initialized. You cannot pass values the second time you call session().');
        }

        return $this->session;
    }

    /**
     * @default {"path":null}
    */
    public function translations(array $value = []): \Symfony\Config\PimcoreAdmin\TranslationsConfig
    {
        if (null === $this->translations) {
            $this->_usedProperties['translations'] = true;
            $this->translations = new \Symfony\Config\PimcoreAdmin\TranslationsConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "translations()" has already been initialized. You cannot pass values the second time you call translations().');
        }

        return $this->translations;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     *
     * @return $this
     */
    public function securityFirewall(mixed $value): static
    {
        $this->_usedProperties['securityFirewall'] = true;
        $this->securityFirewall = $value;

        return $this;
    }

    /**
     * @default {"admin_system_settings":{"write_target":{"type":"symfony-config","options":{"directory":"\/var\/www\/iwapim\/var\/config\/admin_system_settings"}},"read_target":{"type":null,"options":{"directory":null}}}}
    */
    public function configLocation(array $value = []): \Symfony\Config\PimcoreAdmin\ConfigLocationConfig
    {
        if (null === $this->configLocation) {
            $this->_usedProperties['configLocation'] = true;
            $this->configLocation = new \Symfony\Config\PimcoreAdmin\ConfigLocationConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "configLocation()" has already been initialized. You cannot pass values the second time you call configLocation().');
        }

        return $this->configLocation;
    }

    public function getExtensionAlias(): string
    {
        return 'pimcore_admin';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('gdpr_data_extractor', $value)) {
            $this->_usedProperties['gdprDataExtractor'] = true;
            $this->gdprDataExtractor = new \Symfony\Config\PimcoreAdmin\GdprDataExtractorConfig($value['gdpr_data_extractor']);
            unset($value['gdpr_data_extractor']);
        }

        if (array_key_exists('objects', $value)) {
            $this->_usedProperties['objects'] = true;
            $this->objects = new \Symfony\Config\PimcoreAdmin\ObjectsConfig($value['objects']);
            unset($value['objects']);
        }

        if (array_key_exists('assets', $value)) {
            $this->_usedProperties['assets'] = true;
            $this->assets = new \Symfony\Config\PimcoreAdmin\AssetsConfig($value['assets']);
            unset($value['assets']);
        }

        if (array_key_exists('documents', $value)) {
            $this->_usedProperties['documents'] = true;
            $this->documents = new \Symfony\Config\PimcoreAdmin\DocumentsConfig($value['documents']);
            unset($value['documents']);
        }

        if (array_key_exists('notifications', $value)) {
            $this->_usedProperties['notifications'] = true;
            $this->notifications = new \Symfony\Config\PimcoreAdmin\NotificationsConfig($value['notifications']);
            unset($value['notifications']);
        }

        if (array_key_exists('user', $value)) {
            $this->_usedProperties['user'] = true;
            $this->user = new \Symfony\Config\PimcoreAdmin\UserConfig($value['user']);
            unset($value['user']);
        }

        if (array_key_exists('admin_languages', $value)) {
            $this->_usedProperties['adminLanguages'] = true;
            $this->adminLanguages = $value['admin_languages'];
            unset($value['admin_languages']);
        }

        if (array_key_exists('csrf_protection', $value)) {
            $this->_usedProperties['csrfProtection'] = true;
            $this->csrfProtection = new \Symfony\Config\PimcoreAdmin\CsrfProtectionConfig($value['csrf_protection']);
            unset($value['csrf_protection']);
        }

        if (array_key_exists('admin_csp_header', $value)) {
            $this->_usedProperties['adminCspHeader'] = true;
            $this->adminCspHeader = new \Symfony\Config\PimcoreAdmin\AdminCspHeaderConfig($value['admin_csp_header']);
            unset($value['admin_csp_header']);
        }

        if (array_key_exists('custom_admin_path_identifier', $value)) {
            $this->_usedProperties['customAdminPathIdentifier'] = true;
            $this->customAdminPathIdentifier = $value['custom_admin_path_identifier'];
            unset($value['custom_admin_path_identifier']);
        }

        if (array_key_exists('custom_admin_route_name', $value)) {
            $this->_usedProperties['customAdminRouteName'] = true;
            $this->customAdminRouteName = $value['custom_admin_route_name'];
            unset($value['custom_admin_route_name']);
        }

        if (array_key_exists('branding', $value)) {
            $this->_usedProperties['branding'] = true;
            $this->branding = new \Symfony\Config\PimcoreAdmin\BrandingConfig($value['branding']);
            unset($value['branding']);
        }

        if (array_key_exists('session', $value)) {
            $this->_usedProperties['session'] = true;
            $this->session = new \Symfony\Config\PimcoreAdmin\SessionConfig($value['session']);
            unset($value['session']);
        }

        if (array_key_exists('translations', $value)) {
            $this->_usedProperties['translations'] = true;
            $this->translations = new \Symfony\Config\PimcoreAdmin\TranslationsConfig($value['translations']);
            unset($value['translations']);
        }

        if (array_key_exists('security_firewall', $value)) {
            $this->_usedProperties['securityFirewall'] = true;
            $this->securityFirewall = $value['security_firewall'];
            unset($value['security_firewall']);
        }

        if (array_key_exists('config_location', $value)) {
            $this->_usedProperties['configLocation'] = true;
            $this->configLocation = new \Symfony\Config\PimcoreAdmin\ConfigLocationConfig($value['config_location']);
            unset($value['config_location']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['gdprDataExtractor'])) {
            $output['gdpr_data_extractor'] = $this->gdprDataExtractor->toArray();
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
        if (isset($this->_usedProperties['notifications'])) {
            $output['notifications'] = $this->notifications->toArray();
        }
        if (isset($this->_usedProperties['user'])) {
            $output['user'] = $this->user->toArray();
        }
        if (isset($this->_usedProperties['adminLanguages'])) {
            $output['admin_languages'] = $this->adminLanguages;
        }
        if (isset($this->_usedProperties['csrfProtection'])) {
            $output['csrf_protection'] = $this->csrfProtection->toArray();
        }
        if (isset($this->_usedProperties['adminCspHeader'])) {
            $output['admin_csp_header'] = $this->adminCspHeader->toArray();
        }
        if (isset($this->_usedProperties['customAdminPathIdentifier'])) {
            $output['custom_admin_path_identifier'] = $this->customAdminPathIdentifier;
        }
        if (isset($this->_usedProperties['customAdminRouteName'])) {
            $output['custom_admin_route_name'] = $this->customAdminRouteName;
        }
        if (isset($this->_usedProperties['branding'])) {
            $output['branding'] = $this->branding->toArray();
        }
        if (isset($this->_usedProperties['session'])) {
            $output['session'] = $this->session->toArray();
        }
        if (isset($this->_usedProperties['translations'])) {
            $output['translations'] = $this->translations->toArray();
        }
        if (isset($this->_usedProperties['securityFirewall'])) {
            $output['security_firewall'] = $this->securityFirewall;
        }
        if (isset($this->_usedProperties['configLocation'])) {
            $output['config_location'] = $this->configLocation->toArray();
        }

        return $output;
    }

}
