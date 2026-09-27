<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class LocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly string $defaultLocale = 'en'
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->hasSession() && ($session = $request->getSession())->isStarted()) {
            $locale = $session->get('_locale');
            if ($locale !== null) {
                $request->setLocale($locale);
                $request->attributes->set('_locale', $locale);
            }
        } elseif ($request->hasPreviousSession()) {
            $locale = $request->getSession()->get('_locale');
            if ($locale !== null) {
                $request->setLocale($locale);
                $request->attributes->set('_locale', $locale);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Must be registered after the SessionListener (priority 128) but before LocaleListener (priority 16)
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
