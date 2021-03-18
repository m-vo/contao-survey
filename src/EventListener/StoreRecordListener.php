<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Mvo\ContaoSurvey\Event\SurveySubmittedEvent;
use Terminal42\ServiceAnnotationBundle\Annotation\ServiceTag;

class StoreRecordListener
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @ServiceTag(name="kernel_event_listener", event="Mvo\ContaoSurvey\Event\SurveySubmittedEvent", priority="128")
     */
    public function __invoke(SurveySubmittedEvent $event): void
    {
        if (!$event->getSurvey()->isStoreRecords()) {
            return;
        }

        $this->entityManager->persist($event->getRecord());
        $this->entityManager->flush();
    }
}
