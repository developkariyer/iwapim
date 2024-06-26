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

namespace Pimcore\Bundle\OpenSearchClientBundle;

use Monolog\Logger;
use OpenSearch\Client;
use OpenSearch\ClientBuilder;
use Pimcore\Bundle\OpenSearchClientBundle\LogHandler\Filter404Handler;
use Pimcore\Version;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
final class OpenSearchClientFactory
{
    public static function createOpenSearchClient(LoggerInterface $logger, array $config): Client
    {
        $clientBuilder = new ClientBuilder();
        $clientBuilder->setHosts($config['hosts']);

        // TODO remove if when remove support for Pimcore 10
        if (Version::getMajorVersion() >= 11) {
            if (!$config['log_404_errors'] && $logger instanceof Logger) {
                $logger->pushHandler(new Filter404Handler());
            }

            $clientBuilder->setLogger($logger);
        }

        if (isset($config['username'], $config['password'])) {
            $clientBuilder->setBasicAuthentication($config['username'], $config['password']);
        }

        if (isset($config['ssl_key'], $config['ssl_cert'])) {
            $clientBuilder->setSSLKey($config['ssl_key'], $config['ssl_password'] ?? null);
            $clientBuilder->setSSLCert($config['ssl_cert'], $config['ssl_password'] ?? null);
        }

        if (isset($config['ssl_verification'])) {
            $clientBuilder->setSSLVerification($config['ssl_verification']);
        }

        if (isset($config['aws_region'])) {
            $clientBuilder->setSigV4Region($config['aws_region']);
        }

        if (isset($config['aws_service'])) {
            $clientBuilder->setSigV4Service($config['aws_service']);
        }

        if (isset($config['aws_key'], $config['aws_secret'])) {
            $clientBuilder->setSigV4CredentialProvider([
                'key' => $config['aws_key'],
                'secret' => $config['aws_secret'],
            ]);
        }

        return $clientBuilder->build();
    }
}
