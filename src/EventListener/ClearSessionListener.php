<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\EventListener;

use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ClearSessionListener
{
    public const SESSION_LAST_USED_KEY = '_last_used';

    private NamespacedAttributeBag $storage;
    private int $maxIdleTime;

    public function __construct(NamespacedAttributeBag $storage, int $maxIdleTime)
    {
        $this->storage = $storage;
        $this->maxIdleTime = $maxIdleTime;
    }

    /**
     * Clear the survey storage after being idle for a certain time.
     *
     * This is especially helpful if a user starts a survey but does not finish
     * it. If she continues browsing for longer than $maxIdleTime without
     * returning to the survey form, the storage (and thus likely the session)
     * will be cleared/destroyed and pages served from the HTTP cache again.
     */
    public function __invoke(RequestEvent $event): void
    {
        if (0 === $this->maxIdleTime || !$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->hasSession() || !$request->getSession()->isStarted()) {
            return;
        }

        $lastUsed = $this->storage->get(self::SESSION_LAST_USED_KEY);

        if (null !== $lastUsed && ($lastUsed + $this->maxIdleTime < time())) {
            $this->storage->clear();
        }
    }
}
