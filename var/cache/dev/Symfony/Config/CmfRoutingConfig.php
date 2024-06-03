<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'CmfRouting'.\DIRECTORY_SEPARATOR.'ChainConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'CmfRouting'.\DIRECTORY_SEPARATOR.'DynamicConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class CmfRoutingConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $chain;
    private $dynamic;
    private $_usedProperties = [];

    /**
     * @default {"routers_by_id":{"router.default":100},"replace_symfony_router":true}
    */
    public function chain(array $value = []): \Symfony\Config\CmfRouting\ChainConfig
    {
        if (null === $this->chain) {
            $this->_usedProperties['chain'] = true;
            $this->chain = new \Symfony\Config\CmfRouting\ChainConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "chain()" has already been initialized. You cannot pass values the second time you call chain().');
        }

        return $this->chain;
    }

    /**
     * @template TValue
     * @param TValue $value
     * @default {"enabled":false,"route_collection_limit":0,"generic_controller":null,"default_controller":null,"controllers_by_type":[],"controllers_by_class":[],"templates_by_class":[],"persistence":{"phpcr":{"enabled":false,"manager_name":null,"route_basepaths":["\/cms\/routes"],"enable_initializer":true},"orm":{"enabled":false,"manager_name":null,"route_class":"Symfony\\Cmf\\Bundle\\RoutingBundle\\Doctrine\\Orm\\Route"}},"uri_filter_regexp":"","route_filters_by_id":[],"locales":[],"limit_candidates":20,"match_implicit_locale":true,"redirectable_url_matcher":false,"auto_locale_pattern":false,"url_generator":"cmf_routing.generator"}
     * @return \Symfony\Config\CmfRouting\DynamicConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\CmfRouting\DynamicConfig : static)
     */
    public function dynamic(array $value = []): \Symfony\Config\CmfRouting\DynamicConfig|static
    {
        if (!\is_array($value)) {
            $this->_usedProperties['dynamic'] = true;
            $this->dynamic = $value;

            return $this;
        }

        if (!$this->dynamic instanceof \Symfony\Config\CmfRouting\DynamicConfig) {
            $this->_usedProperties['dynamic'] = true;
            $this->dynamic = new \Symfony\Config\CmfRouting\DynamicConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "dynamic()" has already been initialized. You cannot pass values the second time you call dynamic().');
        }

        return $this->dynamic;
    }

    public function getExtensionAlias(): string
    {
        return 'cmf_routing';
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('chain', $value)) {
            $this->_usedProperties['chain'] = true;
            $this->chain = new \Symfony\Config\CmfRouting\ChainConfig($value['chain']);
            unset($value['chain']);
        }

        if (array_key_exists('dynamic', $value)) {
            $this->_usedProperties['dynamic'] = true;
            $this->dynamic = \is_array($value['dynamic']) ? new \Symfony\Config\CmfRouting\DynamicConfig($value['dynamic']) : $value['dynamic'];
            unset($value['dynamic']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['chain'])) {
            $output['chain'] = $this->chain->toArray();
        }
        if (isset($this->_usedProperties['dynamic'])) {
            $output['dynamic'] = $this->dynamic instanceof \Symfony\Config\CmfRouting\DynamicConfig ? $this->dynamic->toArray() : $this->dynamic;
        }

        return $output;
    }

}
