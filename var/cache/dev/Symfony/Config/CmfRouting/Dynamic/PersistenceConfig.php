<?php

namespace Symfony\Config\CmfRouting\Dynamic;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Persistence'.\DIRECTORY_SEPARATOR.'PhpcrConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'Persistence'.\DIRECTORY_SEPARATOR.'OrmConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class PersistenceConfig 
{
    private $phpcr;
    private $orm;
    private $_usedProperties = [];

    /**
     * @template TValue
     * @param TValue $value
     * @default {"enabled":false,"manager_name":null,"route_basepaths":["\/cms\/routes"],"enable_initializer":true}
     * @return \Symfony\Config\CmfRouting\Dynamic\Persistence\PhpcrConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\CmfRouting\Dynamic\Persistence\PhpcrConfig : static)
     */
    public function phpcr(array $value = []): \Symfony\Config\CmfRouting\Dynamic\Persistence\PhpcrConfig|static
    {
        if (!\is_array($value)) {
            $this->_usedProperties['phpcr'] = true;
            $this->phpcr = $value;

            return $this;
        }

        if (!$this->phpcr instanceof \Symfony\Config\CmfRouting\Dynamic\Persistence\PhpcrConfig) {
            $this->_usedProperties['phpcr'] = true;
            $this->phpcr = new \Symfony\Config\CmfRouting\Dynamic\Persistence\PhpcrConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "phpcr()" has already been initialized. You cannot pass values the second time you call phpcr().');
        }

        return $this->phpcr;
    }

    /**
     * @template TValue
     * @param TValue $value
     * @default {"enabled":false,"manager_name":null,"route_class":"Symfony\\Cmf\\Bundle\\RoutingBundle\\Doctrine\\Orm\\Route"}
     * @return \Symfony\Config\CmfRouting\Dynamic\Persistence\OrmConfig|$this
     * @psalm-return (TValue is array ? \Symfony\Config\CmfRouting\Dynamic\Persistence\OrmConfig : static)
     */
    public function orm(array $value = []): \Symfony\Config\CmfRouting\Dynamic\Persistence\OrmConfig|static
    {
        if (!\is_array($value)) {
            $this->_usedProperties['orm'] = true;
            $this->orm = $value;

            return $this;
        }

        if (!$this->orm instanceof \Symfony\Config\CmfRouting\Dynamic\Persistence\OrmConfig) {
            $this->_usedProperties['orm'] = true;
            $this->orm = new \Symfony\Config\CmfRouting\Dynamic\Persistence\OrmConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "orm()" has already been initialized. You cannot pass values the second time you call orm().');
        }

        return $this->orm;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('phpcr', $value)) {
            $this->_usedProperties['phpcr'] = true;
            $this->phpcr = \is_array($value['phpcr']) ? new \Symfony\Config\CmfRouting\Dynamic\Persistence\PhpcrConfig($value['phpcr']) : $value['phpcr'];
            unset($value['phpcr']);
        }

        if (array_key_exists('orm', $value)) {
            $this->_usedProperties['orm'] = true;
            $this->orm = \is_array($value['orm']) ? new \Symfony\Config\CmfRouting\Dynamic\Persistence\OrmConfig($value['orm']) : $value['orm'];
            unset($value['orm']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['phpcr'])) {
            $output['phpcr'] = $this->phpcr instanceof \Symfony\Config\CmfRouting\Dynamic\Persistence\PhpcrConfig ? $this->phpcr->toArray() : $this->phpcr;
        }
        if (isset($this->_usedProperties['orm'])) {
            $output['orm'] = $this->orm instanceof \Symfony\Config\CmfRouting\Dynamic\Persistence\OrmConfig ? $this->orm->toArray() : $this->orm;
        }

        return $output;
    }

}
