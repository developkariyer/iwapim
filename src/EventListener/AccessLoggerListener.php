<?php
// src/EventListener/AccessLoggerListener.php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\SecurityBundle\Security;

class AccessLoggerListener implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private Security $security;

    public function __construct(LoggerInterface $accessLogger, Security $security)
    {
        $this->logger = $accessLogger;
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controllerCallable = $event->getController();
        if (is_array($controllerCallable)) {
            $controllerClass = get_class($controllerCallable[0]);
            $controllerMethod = $controllerCallable[1];
        } elseif (is_object($controllerCallable) && method_exists($controllerCallable, '__invoke')) {
            $controllerClass = get_class($controllerCallable);
            $controllerMethod = '__invoke';
        } else {
            $this->logger->warning('AccessLogger: Anlaşılamayan controller tipi.', ['controller' => $controllerCallable]);
            return;
        }

        $request = $event->getRequest();

        $routeName = $request->attributes->get('_route');

        $user = $this->security->getUser();

        $logMessage = sprintf(
            'Access Granted: Route "%s" (%s::%s)',
            $routeName ?? 'N/A',
            $controllerClass,
            $controllerMethod
        );
        $logContext = [
            'user_id' => $user ? $user->getId() : 'ANONYMOUS',
            'username' => $user ? $user->getUserIdentifier() : 'ANONYMOUS',
            'roles' => $user ? $user->getRoles() : ['IS_AUTHENTICATED_ANONYMOUSLY'],
            'ip_address' => $request->getClientIp(),
            'method' => $request->getMethod(),
            'uri' => $request->getRequestUri(),
        ];

        $this->logger->info($logMessage, $logContext);
    }
}