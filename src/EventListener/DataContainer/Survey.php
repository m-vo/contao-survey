<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Mvo\ContaoSurvey\Repository\RecordRepository;
use Terminal42\ServiceAnnotationBundle\ServiceAnnotationInterface;

class Survey implements ServiceAnnotationInterface
{
    private RecordRepository $recordRepository;

    public function __construct(RecordRepository $recordRepository)
    {
        $this->recordRepository = $recordRepository;
    }

    /**
     * @Callback(table="tl_survey", target="list.label.label")
     */
    public function listSurveys(array $row, string $label): string
    {
        $submittedRecordsCount = $this->recordRepository->countBySurveyId((int) $row['id']);

        return sprintf('%s <span class="survey_answer_count">(%d)</span>', $label, $submittedRecordsCount);
    }
}
