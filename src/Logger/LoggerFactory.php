<?php
namespace App\Logger;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    public static function create(string $marketplace, string $channel): LoggerInterface
    {
        $logger = new Logger($channel);
        $logDir = dirname(__DIR__, 2) . '/var/log/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $filePath = "{$logDir}/{$marketplace}/{$channel}_" . date('Y-m-d_H-i-s') . ".log";
        $logger->pushHandler(new StreamHandler($filePath, Logger::DEBUG));
        return $logger;
    }
}