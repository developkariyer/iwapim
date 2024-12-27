<?php

namespace App\EventListener;

use App\Exception\FrontendException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class FrontendErrorListener
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof FrontendException) {
            $this->flashBag->add('danger', $exception->getMessage());
        }
    }
}
