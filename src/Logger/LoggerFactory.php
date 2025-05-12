<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    public static function create(string $channel): LoggerInterface
    {
        $logger = new Logger($channel);
        $logDir = PIMCORE_PROJECT_ROOT . '/var/log/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $filePath = "{$logDir}/{$channel}_" . date('Y-m-d') . ".log";
        $logger->pushHandler(new StreamHandler($filePath, Logger::DEBUG));
        return $logger;
    }
}