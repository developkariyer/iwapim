<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'SchebTwoFactor'.\DIRECTORY_SEPARATOR.'GoogleConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class SchebTwoFactorConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $persister;
    private $modelManagerName;
    private $securityTokens;
    private $ipWhitelist;
    private $ipWhitelistProvider;
    private $twoFactorTokenFactory;
    private $twoFactorCondition;
    private $google;
    private $_usedProperties = [];

    /**
     * @default 'scheb_two_factor.persister.doctrine'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function persister($value): static
    {
        $this->_usedProperties['persister'] = true;
        $this->persister = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function modelManagerName($value): static
    {
        $this->_usedProperties['modelManagerName'] = true;
        $this->modelManagerName = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function securityTokens(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['securityTokens'] = true;
        $this->securityTokens = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function ipWhitelist(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['ipWhitelist'] = true;
        $this->ipWhitelist = $value;

        return $this;
    }

    /**
     * @default 'scheb_two_factor.default_ip_whitelist_provider'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function ipWhitelistProvider($value): static
    {
        $this->_usedProperties['ipWhitelistProvider'] = true;
        $this->ipWhitelistProvider = $value;

        return $this;
    }

    /**
     * @default 'scheb_two_factor.default_token_factory'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function twoFactorTokenFactory($value): static
    {
        $this->_usedProperties['twoFactorTokenFactory'] = true;
        $this->twoFactorTokenFactory = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function twoFactorCondition($value): static
    {
        $this->_usedProperties['twoFactorCondition'] = true;
        $this->twoFactorCondition = $value;

        return $this;
    }

    /**
     * @template TValue
     * @param TValue $value
     * @default {"enabled":false,"form_renderer":null,"issuer":null,"server_name":null,"template":"@SchebTwoFactor\/Authentication\/form.html.twig","digits":6,"window":1,"leeway":null}
     * @return \Symfony\Config\SchebTwoFactor\GoogleConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\SchebTwoFactor\GoogleConfig : static)
     */
    public function google(array $value = []): \Symfony\Config\SchebTwoFactor\GoogleConfig|static
    {
        if (!\is_array($value)) {
            $this->_usedProperties['google'] = true;
            $this->google = $value;

            return $this;
        }

        if (!$this->google instanceof \Symfony\Config\SchebTwoFactor\GoogleConfig) {
            $this->_usedProperties['google'] = true;
            $this->google = new \Symfony\Config\SchebTwoFactor\GoogleConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "google()" has already been initialized. You cannot pass values the second time you call google().');
        }

        return $this->google;
    }

    public function getExtensionAlias(): string
    {
        return 'scheb_two_factor';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('persister', $value)) {
            $this->_usedProperties['persister'] = true;
            $this->persister = $value['persister'];
            unset($value['persister']);
        }

        if (array_key_exists('model_manager_name', $value)) {
            $this->_usedProperties['modelManagerName'] = true;
            $this->modelManagerName = $value['model_manager_name'];
            unset($value['model_manager_name']);
        }

        if (array_key_exists('security_tokens', $value)) {
            $this->_usedProperties['securityTokens'] = true;
            $this->securityTokens = $value['security_tokens'];
            unset($value['security_tokens']);
        }

        if (array_key_exists('ip_whitelist', $value)) {
            $this->_usedProperties['ipWhitelist'] = true;
            $this->ipWhitelist = $value['ip_whitelist'];
            unset($value['ip_whitelist']);
        }

        if (array_key_exists('ip_whitelist_provider', $value)) {
            $this->_usedProperties['ipWhitelistProvider'] = true;
            $this->ipWhitelistProvider = $value['ip_whitelist_provider'];
            unset($value['ip_whitelist_provider']);
        }

        if (array_key_exists('two_factor_token_factory', $value)) {
            $this->_usedProperties['twoFactorTokenFactory'] = true;
            $this->twoFactorTokenFactory = $value['two_factor_token_factory'];
            unset($value['two_factor_token_factory']);
        }

        if (array_key_exists('two_factor_condition', $value)) {
            $this->_usedProperties['twoFactorCondition'] = true;
            $this->twoFactorCondition = $value['two_factor_condition'];
            unset($value['two_factor_condition']);
        }

        if (array_key_exists('google', $value)) {
            $this->_usedProperties['google'] = true;
            $this->google = \is_array($value['google']) ? new \Symfony\Config\SchebTwoFactor\GoogleConfig($value['google']) : $value['google'];
            unset($value['google']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['persister'])) {
            $output['persister'] = $this->persister;
        }
        if (isset($this->_usedProperties['modelManagerName'])) {
            $output['model_manager_name'] = $this->modelManagerName;
        }
        if (isset($this->_usedProperties['securityTokens'])) {
            $output['security_tokens'] = $this->securityTokens;
        }
        if (isset($this->_usedProperties['ipWhitelist'])) {
            $output['ip_whitelist'] = $this->ipWhitelist;
        }
        if (isset($this->_usedProperties['ipWhitelistProvider'])) {
            $output['ip_whitelist_provider'] = $this->ipWhitelistProvider;
        }
        if (isset($this->_usedProperties['twoFactorTokenFactory'])) {
            $output['two_factor_token_factory'] = $this->twoFactorTokenFactory;
        }
        if (isset($this->_usedProperties['twoFactorCondition'])) {
            $output['two_factor_condition'] = $this->twoFactorCondition;
        }
        if (isset($this->_usedProperties['google'])) {
            $output['google'] = $this->google instanceof \Symfony\Config\SchebTwoFactor\GoogleConfig ? $this->google->toArray() : $this->google;
        }

        return $output;
    }

}
