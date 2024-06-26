<?php
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under following license:
 * - GNU General Public License version 3 (GPLv3)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3
 */

namespace Pimcore\Bundle\OpenSearchClientBundle\LogHandler;

use Monolog\Handler\AbstractHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Pimcore\Version;

// TODO remove if when remove support for Pimcore 10
if(Version::getMajorVersion() >= 11) {

    /**
     * Ignores warning messages for 404 errors as they are spamming the logs
     *
     * @internal
     */
    final class Filter404Handler extends AbstractHandler
    {
        private bool $ignoreNextResponseWarning = false;

        public function isHandling(LogRecord $record): bool
        {
            $ignore =
                $record->level === Level::Warning
                && ($record->context['HTTP code'] ?? null) === 404;

            if ($ignore) {
                $this->ignoreNextResponseWarning = true;
            } else {
                $ignore = $this->ignoreNextResponseWarning
                    && $record->level === Level::Warning
                    && $record->message === 'Response';
                $this->ignoreNextResponseWarning = false;
            }

            return $ignore;
        }

        public function handle(LogRecord $record): bool
        {
            return $this->isHandling($record);
        }
    }
}
