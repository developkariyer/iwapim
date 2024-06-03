<?php

namespace Symfony\Config\Pimcore;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Security'.\DIRECTORY_SEPARATOR.'PasswordConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Security'.\DIRECTORY_SEPARATOR.'EncoderFactoriesConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Security'.\DIRECTORY_SEPARATOR.'PasswordHasherFactoriesConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Loader\ParamConfigurator;

/**
 * This class is automatically generated to help in creating a config.
 */
class SecurityConfig 
{
    private $password;
    private $factoryType;
    private $encoderFactories;
    private $passwordHasherFactories;
    private $_usedProperties = [];

    /**
     * @default {"algorithm":"2y","options":[]}
    */
    public function password(array $value = []): \Symfony\Config\Pimcore\Security\PasswordConfig
    {
        if (null === $this->password) {
            $this->_usedProperties['password'] = true;
            $this->password = new \Symfony\Config\Pimcore\Security\PasswordConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "password()" has already been initialized. You cannot pass values the second time you call password().');
        }

        return $this->password;
    }

    /**
     * @default 'encoder'
     * @param ParamConfigurator|'encoder'|'password_hasher' $value
     * @return $this
     */
    public function factoryType($value): static
    {
        $this->_usedProperties['factoryType'] = true;
        $this->factoryType = $value;

        return $this;
    }

    /**
     * @template TValue
     * @param TValue $value
     * Encoder factories to use as className => factory service ID mapping
     * @example {"id":"website_demo.security.encoder_factory2"}
     * @example "website_demo.security.encoder_factory2"
     * @return \Symfony\Config\Pimcore\Security\EncoderFactoriesConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\Pimcore\Security\EncoderFactoriesConfig : static)
     */
    public function encoderFactories(string $class, string|array $value = []): \Symfony\Config\Pimcore\Security\EncoderFactoriesConfig|static
    {
        if (!\is_array($value)) {
            $this->_usedProperties['encoderFactories'] = true;
            $this->encoderFactories[$class] = $value;

            return $this;
        }

        if (!isset($this->encoderFactories[$class]) || !$this->encoderFactories[$class] instanceof \Symfony\Config\Pimcore\Security\EncoderFactoriesConfig) {
            $this->_usedProperties['encoderFactories'] = true;
            $this->encoderFactories[$class] = new \Symfony\Config\Pimcore\Security\EncoderFactoriesConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "encoderFactories()" has already been initialized. You cannot pass values the second time you call encoderFactories().');
        }

        return $this->encoderFactories[$class];
    }

    /**
     * @template TValue
     * @param TValue $value
     * Password hasher factories to use as className => factory service ID mapping
     * @example {"id":"website_demo.security.encoder_factory2"}
     * @example "website_demo.security.encoder_factory2"
     * @return \Symfony\Config\Pimcore\Security\PasswordHasherFactoriesConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\Pimcore\Security\PasswordHasherFactoriesConfig : static)
     */
    public function passwordHasherFactories(string $class, string|array $value = []): \Symfony\Config\Pimcore\Security\PasswordHasherFactoriesConfig|static
    {
        if (!\is_array($value)) {
            $this->_usedProperties['passwordHasherFactories'] = true;
            $this->passwordHasherFactories[$class] = $value;

            return $this;
        }

        if (!isset($this->passwordHasherFactories[$class]) || !$this->passwordHasherFactories[$class] instanceof \Symfony\Config\Pimcore\Security\PasswordHasherFactoriesConfig) {
            $this->_usedProperties['passwordHasherFactories'] = true;
            $this->passwordHasherFactories[$class] = new \Symfony\Config\Pimcore\Security\PasswordHasherFactoriesConfig($value);
        } elseif (1 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "passwordHasherFactories()" has already been initialized. You cannot pass values the second time you call passwordHasherFactories().');
        }

        return $this->passwordHasherFactories[$class];
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('password', $value)) {
            $this->_usedProperties['password'] = true;
            $this->password = new \Symfony\Config\Pimcore\Security\PasswordConfig($value['password']);
            unset($value['password']);
        }

        if (array_key_exists('factory_type', $value)) {
            $this->_usedProperties['factoryType'] = true;
            $this->factoryType = $value['factory_type'];
            unset($value['factory_type']);
        }

        if (array_key_exists('encoder_factories', $value)) {
            $this->_usedProperties['encoderFactories'] = true;
            $this->encoderFactories = array_map(fn ($v) => \is_array($v) ? new \Symfony\Config\Pimcore\Security\EncoderFactoriesConfig($v) : $v, $value['encoder_factories']);
            unset($value['encoder_factories']);
        }

        if (array_key_exists('password_hasher_factories', $value)) {
            $this->_usedProperties['passwordHasherFactories'] = true;
            $this->passwordHasherFactories = array_map(fn ($v) => \is_array($v) ? new \Symfony\Config\Pimcore\Security\PasswordHasherFactoriesConfig($v) : $v, $value['password_hasher_factories']);
            unset($value['password_hasher_factories']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['password'])) {
            $output['password'] = $this->password->toArray();
        }
        if (isset($this->_usedProperties['factoryType'])) {
            $output['factory_type'] = $this->factoryType;
        }
        if (isset($this->_usedProperties['encoderFactories'])) {
            $output['encoder_factories'] = array_map(fn ($v) => $v instanceof \Symfony\Config\Pimcore\Security\EncoderFactoriesConfig ? $v->toArray() : $v, $this->encoderFactories);
        }
        if (isset($this->_usedProperties['passwordHasherFactories'])) {
            $output['password_hasher_factories'] = array_map(fn ($v) => $v instanceof \Symfony\Config\Pimcore\Security\PasswordHasherFactoriesConfig ? $v->toArray() : $v, $this->passwordHasherFactories);
        }

        return $output;
    }

}
