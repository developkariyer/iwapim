<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'PimcoreInstall'.\DIRECTORY_SEPARATOR.'ParametersConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class PimcoreInstallConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $infoMessage;
    private $parameters;
    private $_usedProperties = [];

    /**
     * Shows an info message on the installation screen
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function infoMessage($value): static
    {
        $this->_usedProperties['infoMessage'] = true;
        $this->infoMessage = $value;

        return $this;
    }

    /**
     * @default {"database_credentials":[]}
    */
    public function parameters(array $value = []): \Symfony\Config\PimcoreInstall\ParametersConfig
    {
        if (null === $this->parameters) {
            $this->_usedProperties['parameters'] = true;
            $this->parameters = new \Symfony\Config\PimcoreInstall\ParametersConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "parameters()" has already been initialized. You cannot pass values the second time you call parameters().');
        }

        return $this->parameters;
    }

    public function getExtensionAlias(): string
    {
        return 'pimcore_install';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('info_message', $value)) {
            $this->_usedProperties['infoMessage'] = true;
            $this->infoMessage = $value['info_message'];
            unset($value['info_message']);
        }

        if (array_key_exists('parameters', $value)) {
            $this->_usedProperties['parameters'] = true;
            $this->parameters = new \Symfony\Config\PimcoreInstall\ParametersConfig($value['parameters']);
            unset($value['parameters']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['infoMessage'])) {
            $output['info_message'] = $this->infoMessage;
        }
        if (isset($this->_usedProperties['parameters'])) {
            $output['parameters'] = $this->parameters->toArray();
        }

        return $output;
    }

}
