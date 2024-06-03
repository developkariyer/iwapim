<?php

namespace Symfony\Config\Security\FirewallConfig;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class TwoFactorConfig 
{
    private $checkPath;
    private $postOnly;
    private $authFormPath;
    private $alwaysUseDefaultTargetPath;
    private $defaultTargetPath;
    private $successHandler;
    private $failureHandler;
    private $authenticationRequiredHandler;
    private $authCodeParameterName;
    private $trustedParameterName;
    private $rememberMeSetsTrusted;
    private $multiFactor;
    private $prepareOnLogin;
    private $prepareOnAccessDenied;
    private $enableCsrf;
    private $csrfParameter;
    private $csrfTokenId;
    private $provider;
    private $_usedProperties = [];

    /**
     * @default '/2fa_check'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function checkPath($value): static
    {
        $this->_usedProperties['checkPath'] = true;
        $this->checkPath = $value;

        return $this;
    }

    /**
     * @default true
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function postOnly($value): static
    {
        $this->_usedProperties['postOnly'] = true;
        $this->postOnly = $value;

        return $this;
    }

    /**
     * @default '/2fa'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function authFormPath($value): static
    {
        $this->_usedProperties['authFormPath'] = true;
        $this->authFormPath = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function alwaysUseDefaultTargetPath($value): static
    {
        $this->_usedProperties['alwaysUseDefaultTargetPath'] = true;
        $this->alwaysUseDefaultTargetPath = $value;

        return $this;
    }

    /**
     * @default '/'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function defaultTargetPath($value): static
    {
        $this->_usedProperties['defaultTargetPath'] = true;
        $this->defaultTargetPath = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function successHandler($value): static
    {
        $this->_usedProperties['successHandler'] = true;
        $this->successHandler = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function failureHandler($value): static
    {
        $this->_usedProperties['failureHandler'] = true;
        $this->failureHandler = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function authenticationRequiredHandler($value): static
    {
        $this->_usedProperties['authenticationRequiredHandler'] = true;
        $this->authenticationRequiredHandler = $value;

        return $this;
    }

    /**
     * @default '_auth_code'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function authCodeParameterName($value): static
    {
        $this->_usedProperties['authCodeParameterName'] = true;
        $this->authCodeParameterName = $value;

        return $this;
    }

    /**
     * @default '_trusted'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function trustedParameterName($value): static
    {
        $this->_usedProperties['trustedParameterName'] = true;
        $this->trustedParameterName = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function rememberMeSetsTrusted($value): static
    {
        $this->_usedProperties['rememberMeSetsTrusted'] = true;
        $this->rememberMeSetsTrusted = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function multiFactor($value): static
    {
        $this->_usedProperties['multiFactor'] = true;
        $this->multiFactor = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function prepareOnLogin($value): static
    {
        $this->_usedProperties['prepareOnLogin'] = true;
        $this->prepareOnLogin = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function prepareOnAccessDenied($value): static
    {
        $this->_usedProperties['prepareOnAccessDenied'] = true;
        $this->prepareOnAccessDenied = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function enableCsrf($value): static
    {
        $this->_usedProperties['enableCsrf'] = true;
        $this->enableCsrf = $value;

        return $this;
    }

    /**
     * @default '_csrf_token'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function csrfParameter($value): static
    {
        $this->_usedProperties['csrfParameter'] = true;
        $this->csrfParameter = $value;

        return $this;
    }

    /**
     * @default 'two_factor'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function csrfTokenId($value): static
    {
        $this->_usedProperties['csrfTokenId'] = true;
        $this->csrfTokenId = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function provider($value): static
    {
        $this->_usedProperties['provider'] = true;
        $this->provider = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('check_path', $value)) {
            $this->_usedProperties['checkPath'] = true;
            $this->checkPath = $value['check_path'];
            unset($value['check_path']);
        }

        if (array_key_exists('post_only', $value)) {
            $this->_usedProperties['postOnly'] = true;
            $this->postOnly = $value['post_only'];
            unset($value['post_only']);
        }

        if (array_key_exists('auth_form_path', $value)) {
            $this->_usedProperties['authFormPath'] = true;
            $this->authFormPath = $value['auth_form_path'];
            unset($value['auth_form_path']);
        }

        if (array_key_exists('always_use_default_target_path', $value)) {
            $this->_usedProperties['alwaysUseDefaultTargetPath'] = true;
            $this->alwaysUseDefaultTargetPath = $value['always_use_default_target_path'];
            unset($value['always_use_default_target_path']);
        }

        if (array_key_exists('default_target_path', $value)) {
            $this->_usedProperties['defaultTargetPath'] = true;
            $this->defaultTargetPath = $value['default_target_path'];
            unset($value['default_target_path']);
        }

        if (array_key_exists('success_handler', $value)) {
            $this->_usedProperties['successHandler'] = true;
            $this->successHandler = $value['success_handler'];
            unset($value['success_handler']);
        }

        if (array_key_exists('failure_handler', $value)) {
            $this->_usedProperties['failureHandler'] = true;
            $this->failureHandler = $value['failure_handler'];
            unset($value['failure_handler']);
        }

        if (array_key_exists('authentication_required_handler', $value)) {
            $this->_usedProperties['authenticationRequiredHandler'] = true;
            $this->authenticationRequiredHandler = $value['authentication_required_handler'];
            unset($value['authentication_required_handler']);
        }

        if (array_key_exists('auth_code_parameter_name', $value)) {
            $this->_usedProperties['authCodeParameterName'] = true;
            $this->authCodeParameterName = $value['auth_code_parameter_name'];
            unset($value['auth_code_parameter_name']);
        }

        if (array_key_exists('trusted_parameter_name', $value)) {
            $this->_usedProperties['trustedParameterName'] = true;
            $this->trustedParameterName = $value['trusted_parameter_name'];
            unset($value['trusted_parameter_name']);
        }

        if (array_key_exists('remember_me_sets_trusted', $value)) {
            $this->_usedProperties['rememberMeSetsTrusted'] = true;
            $this->rememberMeSetsTrusted = $value['remember_me_sets_trusted'];
            unset($value['remember_me_sets_trusted']);
        }

        if (array_key_exists('multi_factor', $value)) {
            $this->_usedProperties['multiFactor'] = true;
            $this->multiFactor = $value['multi_factor'];
            unset($value['multi_factor']);
        }

        if (array_key_exists('prepare_on_login', $value)) {
            $this->_usedProperties['prepareOnLogin'] = true;
            $this->prepareOnLogin = $value['prepare_on_login'];
            unset($value['prepare_on_login']);
        }

        if (array_key_exists('prepare_on_access_denied', $value)) {
            $this->_usedProperties['prepareOnAccessDenied'] = true;
            $this->prepareOnAccessDenied = $value['prepare_on_access_denied'];
            unset($value['prepare_on_access_denied']);
        }

        if (array_key_exists('enable_csrf', $value)) {
            $this->_usedProperties['enableCsrf'] = true;
            $this->enableCsrf = $value['enable_csrf'];
            unset($value['enable_csrf']);
        }

        if (array_key_exists('csrf_parameter', $value)) {
            $this->_usedProperties['csrfParameter'] = true;
            $this->csrfParameter = $value['csrf_parameter'];
            unset($value['csrf_parameter']);
        }

        if (array_key_exists('csrf_token_id', $value)) {
            $this->_usedProperties['csrfTokenId'] = true;
            $this->csrfTokenId = $value['csrf_token_id'];
            unset($value['csrf_token_id']);
        }

        if (array_key_exists('provider', $value)) {
            $this->_usedProperties['provider'] = true;
            $this->provider = $value['provider'];
            unset($value['provider']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['checkPath'])) {
            $output['check_path'] = $this->checkPath;
        }
        if (isset($this->_usedProperties['postOnly'])) {
            $output['post_only'] = $this->postOnly;
        }
        if (isset($this->_usedProperties['authFormPath'])) {
            $output['auth_form_path'] = $this->authFormPath;
        }
        if (isset($this->_usedProperties['alwaysUseDefaultTargetPath'])) {
            $output['always_use_default_target_path'] = $this->alwaysUseDefaultTargetPath;
        }
        if (isset($this->_usedProperties['defaultTargetPath'])) {
            $output['default_target_path'] = $this->defaultTargetPath;
        }
        if (isset($this->_usedProperties['successHandler'])) {
            $output['success_handler'] = $this->successHandler;
        }
        if (isset($this->_usedProperties['failureHandler'])) {
            $output['failure_handler'] = $this->failureHandler;
        }
        if (isset($this->_usedProperties['authenticationRequiredHandler'])) {
            $output['authentication_required_handler'] = $this->authenticationRequiredHandler;
        }
        if (isset($this->_usedProperties['authCodeParameterName'])) {
            $output['auth_code_parameter_name'] = $this->authCodeParameterName;
        }
        if (isset($this->_usedProperties['trustedParameterName'])) {
            $output['trusted_parameter_name'] = $this->trustedParameterName;
        }
        if (isset($this->_usedProperties['rememberMeSetsTrusted'])) {
            $output['remember_me_sets_trusted'] = $this->rememberMeSetsTrusted;
        }
        if (isset($this->_usedProperties['multiFactor'])) {
            $output['multi_factor'] = $this->multiFactor;
        }
        if (isset($this->_usedProperties['prepareOnLogin'])) {
            $output['prepare_on_login'] = $this->prepareOnLogin;
        }
        if (isset($this->_usedProperties['prepareOnAccessDenied'])) {
            $output['prepare_on_access_denied'] = $this->prepareOnAccessDenied;
        }
        if (isset($this->_usedProperties['enableCsrf'])) {
            $output['enable_csrf'] = $this->enableCsrf;
        }
        if (isset($this->_usedProperties['csrfParameter'])) {
            $output['csrf_parameter'] = $this->csrfParameter;
        }
        if (isset($this->_usedProperties['csrfTokenId'])) {
            $output['csrf_token_id'] = $this->csrfTokenId;
        }
        if (isset($this->_usedProperties['provider'])) {
            $output['provider'] = $this->provider;
        }

        return $output;
    }

}
