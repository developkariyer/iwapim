<?php

namespace Symfony\Config\Pimcore\Maintenance;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class HousekeepingConfig 
{
    private $cleanupTmpFilesAtimeOlderThan;
    private $cleanupProfilerFilesAtimeOlderThan;
    private $_usedProperties = [];

    /**
     * Integer value in seconds.
     * @default 7776000
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function cleanupTmpFilesAtimeOlderThan($value): static
    {
        $this->_usedProperties['cleanupTmpFilesAtimeOlderThan'] = true;
        $this->cleanupTmpFilesAtimeOlderThan = $value;

        return $this;
    }

    /**
     * Integer value in seconds.
     * @default 1800
     * @param ParamConfigurator|int $value
     * @return $this
     */
    public function cleanupProfilerFilesAtimeOlderThan($value): static
    {
        $this->_usedProperties['cleanupProfilerFilesAtimeOlderThan'] = true;
        $this->cleanupProfilerFilesAtimeOlderThan = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('cleanup_tmp_files_atime_older_than', $value)) {
            $this->_usedProperties['cleanupTmpFilesAtimeOlderThan'] = true;
            $this->cleanupTmpFilesAtimeOlderThan = $value['cleanup_tmp_files_atime_older_than'];
            unset($value['cleanup_tmp_files_atime_older_than']);
        }

        if (array_key_exists('cleanup_profiler_files_atime_older_than', $value)) {
            $this->_usedProperties['cleanupProfilerFilesAtimeOlderThan'] = true;
            $this->cleanupProfilerFilesAtimeOlderThan = $value['cleanup_profiler_files_atime_older_than'];
            unset($value['cleanup_profiler_files_atime_older_than']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['cleanupTmpFilesAtimeOlderThan'])) {
            $output['cleanup_tmp_files_atime_older_than'] = $this->cleanupTmpFilesAtimeOlderThan;
        }
        if (isset($this->_usedProperties['cleanupProfilerFilesAtimeOlderThan'])) {
            $output['cleanup_profiler_files_atime_older_than'] = $this->cleanupProfilerFilesAtimeOlderThan;
        }

        return $output;
    }

}
