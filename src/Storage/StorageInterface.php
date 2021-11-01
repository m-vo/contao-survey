<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Storage;

use Mvo\ContaoSurvey\Entity\Answer;

interface StorageInterface
{
    public function initialize(): void;

    public function getStepIndex(int $surveyId): int;

    public function setStepIndex(int $surveyId, int $stepIndex): void;

    /**
     * @return array<int,array<string,Answer|null>|null>
     */
    public function getAllAnswers(int $surveyId): array;

    /**
     * @param array<int,Answer|null>|null $stepAnswers
     */
    public function setAnswersForStep(int $surveyId, int $step, ?array $stepAnswers): void;

    public function getHash(int $surveyId): ?string;

    public function setHash(int $surveyId, string $hash): void;

    public function reset(int $surveyId): void;
}
