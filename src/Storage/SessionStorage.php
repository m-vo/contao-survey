<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Storage;

use Mvo\ContaoSurvey\EventListener\ClearSessionListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionStorage implements StorageInterface
{
    private const ATTRIBUTE_PREFIX = 'mvo.survey/';
    private SessionInterface $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function initialize(): void
    {
        $this->session->start();
        $this->session->set(ClearSessionListener::SESSION_LAST_USED_KEY, time());
    }

    public function getStepIndex(int $surveyId): int
    {
        return $this->session->get(self::ATTRIBUTE_PREFIX.$surveyId.'/step', 0);
    }

    public function setStepIndex(int $surveyId, int $stepIndex): void
    {
        $this->session->set(self::ATTRIBUTE_PREFIX.$surveyId.'/step', $stepIndex);
    }

    public function getAllAnswers(int $surveyId): array
    {
        return $this->session->get(self::ATTRIBUTE_PREFIX.$surveyId.'/answers', []);
    }

    public function setAnswersForStep(int $surveyId, int $step, ?array $stepAnswers): void
    {
        $answers = $this->getAllAnswers($surveyId);
        $answers[$step] = $stepAnswers;

        $this->session->set(self::ATTRIBUTE_PREFIX.$surveyId.'/answers', $answers);
    }

    public function getHash(int $surveyId): ?string
    {
        return $this->session->get(self::ATTRIBUTE_PREFIX.$surveyId.'/hash');
    }

    public function setHash(int $surveyId, string $hash): void
    {
        $this->session->set(self::ATTRIBUTE_PREFIX.$surveyId.'/hash', $hash);
    }

    public function reset(int $surveyId): void
    {
        $this->session->remove(self::ATTRIBUTE_PREFIX.$surveyId.'/step');
        $this->session->remove(self::ATTRIBUTE_PREFIX.$surveyId.'/answers');
        $this->session->remove(self::ATTRIBUTE_PREFIX.$surveyId.'/hash');
    }
}
