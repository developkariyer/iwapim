<?php

namespace Symfony\Config\Pimcore;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Translations'.\DIRECTORY_SEPARATOR.'DebuggingConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class TranslationsConfig 
{
    private $domains;
    private $adminTranslationMapping;
    private $debugging;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function domains(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['domains'] = true;
        $this->domains = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function adminTranslationMapping(string $locale, mixed $value): static
    {
        $this->_usedProperties['adminTranslationMapping'] = true;
        $this->adminTranslationMapping[$locale] = $value;

        return $this;
    }

    /**
     * If debugging is enabled, the translator will return the plain translation key instead of the translated message.
     * @default {"enabled":true,"parameter":"pimcore_debug_translations"}
    */
    public function debugging(array $value = []): \Symfony\Config\Pimcore\Translations\DebuggingConfig
    {
        if (null === $this->debugging) {
            $this->_usedProperties['debugging'] = true;
            $this->debugging = new \Symfony\Config\Pimcore\Translations\DebuggingConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "debugging()" has already been initialized. You cannot pass values the second time you call debugging().');
        }

        return $this->debugging;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('domains', $value)) {
            $this->_usedProperties['domains'] = true;
            $this->domains = $value['domains'];
            unset($value['domains']);
        }

        if (array_key_exists('admin_translation_mapping', $value)) {
            $this->_usedProperties['adminTranslationMapping'] = true;
            $this->adminTranslationMapping = $value['admin_translation_mapping'];
            unset($value['admin_translation_mapping']);
        }

        if (array_key_exists('debugging', $value)) {
            $this->_usedProperties['debugging'] = true;
            $this->debugging = new \Symfony\Config\Pimcore\Translations\DebuggingConfig($value['debugging']);
            unset($value['debugging']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['domains'])) {
            $output['domains'] = $this->domains;
        }
        if (isset($this->_usedProperties['adminTranslationMapping'])) {
            $output['admin_translation_mapping'] = $this->adminTranslationMapping;
        }
        if (isset($this->_usedProperties['debugging'])) {
            $output['debugging'] = $this->debugging->toArray();
        }

        return $output;
    }

}
