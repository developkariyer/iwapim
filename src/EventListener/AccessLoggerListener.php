<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Frontend Access Log Event Subscriber
 */
class AccessLoggerListener implements EventSubscriberInterface
{
    /** @var array */
    private const EXCLUDED_ROUTES = [
        '_wdt',
        '_profiler',
        '_profiler_search',
        '_profiler_router',
        'favicon.ico',
    ];

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var Security */
    private Security $security;

    /**
     * @param LoggerInterface $accessLogger Monolog Logger service
     * @param Security $security Security service
     */
    public function __construct(LoggerInterface $accessLogger, Security $security)
    {
        $this->logger = $accessLogger;
        $this->security = $security;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', 0],
        ];
    }

    /**
     * @param ControllerEvent $event
     * @return void
     */
    public function onKernelController(ControllerEvent $event): void
    {
        try {
            $request = $event->getRequest();
            $routeName = $request->attributes->get('_route');

            if ($routeName && in_array($routeName, self::EXCLUDED_ROUTES, true)) {
                return;
            }

            $controllerInfo = $this->getControllerInfo($event->getController());
            if (!$controllerInfo) {
                return;
            }

            $userInfo = $this->getUserInfo();

            $requestInfo = [
                'ip_address' => $request->getClientIp() ?: 'unknown',
                'method' => $request->getMethod(),
                'uri' => $request->getRequestUri(),
                'route' => $routeName ?: 'undefined_route',
                'query_params' => $this->sanitizeQueryParams($request->query->all()),
                'referer' => $request->headers->get('referer'),
                'user_agent' => $request->headers->get('User-Agent'),
            ];

            $logMessage = sprintf(
                'ACCESS: %s %s -> %s::%s',
                $requestInfo['method'],
                $requestInfo['route'],
                $controllerInfo['class'],
                $controllerInfo['method']
            );

            $logContext = array_merge($userInfo, $requestInfo, $controllerInfo);
            $this->logger->info($logMessage, $logContext);

        } catch (\Throwable $e) {
            $this->logger->error('AccessLogger hata: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
    }

    /**
     * @param mixed $controller
     * @return array|null
     */
    private function getControllerInfo(mixed $controller): ?array
    {
        if (is_array($controller)) {
            return [
                'class' => get_class($controller[0]),
                'method' => $controller[1],
            ];
        } elseif (is_object($controller) && method_exists($controller, '__invoke')) {
            return [
                'class' => get_class($controller),
                'method' => '__invoke',
            ];
        }

        $this->logger->warning('AccessLogger: Anlaşılamayan controller tipi.', [
            'controller_type' => get_debug_type($controller),
        ]);

        return null;
    }

    /**
     * @return array
     */
    private function getUserInfo(): array
    {
        try {
            $user = $this->security->getUser();

            if (!$user) {
                return [
                    'user_id' => 'ANONYMOUS',
                    'username' => 'ANONYMOUS',
                    'roles' => ['IS_AUTHENTICATED_ANONYMOUSLY'],
                    'authenticated' => false,
                ];
            }

            return [
                'user_id' => method_exists($user, 'getId') ? $user->getId() : 'N/A',
                'username' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
                'authenticated' => true,
            ];

        } catch (\Throwable $e) {
            $this->logger->warning('AccessLogger kullanıcı bilgisi hata: ' . $e->getMessage());

            return [
                'user_id' => 'ERROR',
                'username' => 'ERROR',
                'roles' => [],
                'authenticated' => false,
            ];
        }
    }

    /**
     * @param array $queryParams
     * @return array
     */
    private function sanitizeQueryParams(array $queryParams): array
    {
        $sensitiveParams = ['password', 'token', 'key', 'secret', 'auth'];

        foreach ($queryParams as $key => $value) {
            if (is_string($key) && $this->containsSensitiveData($key, $sensitiveParams)) {
                $queryParams[$key] = '[FILTERED]';
            }
        }

        return $queryParams;
    }

    /**
     * @param string $key
     * @param array $sensitiveWords
     * @return bool
     */
    private function containsSensitiveData(string $key, array $sensitiveWords): bool
    {
        $key = strtolower($key);

        foreach ($sensitiveWords as $word) {
            if (str_contains($key, $word)) {
                return true;
            }
        }

        return false;
    }
}