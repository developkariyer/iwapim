<?php

namespace Symfony\Config\Pimcore;

require_once __DIR__.\DIRECTORY_SEPARATOR.'TemplatingEngine'.\DIRECTORY_SEPARATOR.'TwigConfig.php';

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class TemplatingEngineConfig 
{
    private $twig;
    private $_usedProperties = [];

    public function twig(array $value = []): \Symfony\Config\Pimcore\TemplatingEngine\TwigConfig
    {
        if (null === $this->twig) {
            $this->_usedProperties['twig'] = true;
            $this->twig = new \Symfony\Config\Pimcore\TemplatingEngine\TwigConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "twig()" has already been initialized. You cannot pass values the second time you call twig().');
        }

        return $this->twig;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('twig', $value)) {
            $this->_usedProperties['twig'] = true;
            $this->twig = new \Symfony\Config\Pimcore\TemplatingEngine\TwigConfig($value['twig']);
            unset($value['twig']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['twig'])) {
            $output['twig'] = $this->twig->toArray();
        }

        return $output;
    }

}
