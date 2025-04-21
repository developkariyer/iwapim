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
    /** @var array Loglanmaması gereken rotalar */
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
     * @param LoggerInterface $accessLogger Monolog Logger servisi
     * @param Security $security Security servisi
     */
    public function __construct(LoggerInterface $accessLogger, Security $security)
    {
        $this->logger = $accessLogger;
        $this->security = $security;
    }

    /**
     * Abone olunan olayları tanımlar
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', 0],
        ];
    }

    /**
     * Controller çağrıldığında erişim bilgilerini loglar
     *
     * @param ControllerEvent $event Controller olayı
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
     * Controller nesnesinden sınıf ve metod bilgilerini çıkarır
     *
     * @param mixed $controller Controller nesnesi
     * @return array|null Sınıf ve metod bilgileri ya da null
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
     * Güvenli bir şekilde kullanıcı bilgilerini toplar
     *
     * @return array Kullanıcı bilgileri
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
     * Query parametrelerinden hassas bilgileri temizler
     *
     * @param array $queryParams Query parametreleri
     * @return array Temizlenmiş query parametreleri
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
     * String içinde hassas veri içeren anahtar kelime kontrolü
     *
     * @param string $key Kontrol edilecek anahtar
     * @param array $sensitiveWords Hassas kelimeler listesi
     * @return bool Hassas veri içeriyor mu
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