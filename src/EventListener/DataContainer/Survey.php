<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Doctrine\ORM\EntityManagerInterface;
use Mvo\ContaoSurvey\Entity\Survey as SurveyEntity;
use Mvo\ContaoSurvey\Repository\RecordRepository;
use Mvo\ContaoSurvey\Repository\SurveyRepository;
use Terminal42\ServiceAnnotationBundle\ServiceAnnotationInterface;

class Survey implements ServiceAnnotationInterface
{
    private SurveyRepository $surveyRepository;
    private RecordRepository $recordRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(SurveyRepository $surveyRepository, RecordRepository $recordRepository, EntityManagerInterface $entityManager)
    {
        $this->surveyRepository = $surveyRepository;
        $this->recordRepository = $recordRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Callback(table="tl_survey", target="list.label.label")
     */
    public function listSurveys(array $row, string $label): string
    {
        $submittedRecordsCount = $this->recordRepository->countBySurveyId((int) $row['id']);

        return sprintf('%s <span class="survey_answer_count">(%d)</span>', $label, $submittedRecordsCount);
    }

    /**
     * @Callback(table="tl_survey", target="config.onsubmit")
     */
    public function resetOnPublish(DataContainer $dataContainer): void
    {
        if (null === $dataContainer->activeRecord) {
            return;
        }

        /** @var SurveyEntity $survey */
        $survey = $this->surveyRepository->find((int) $dataContainer->id);

        // Use active record to make sure it's the latest value
        if (!$dataContainer->activeRecord->published) {
            $survey->resetCleared();
        } elseif (!$survey->isCleared()) {
            foreach ($survey->getRecords() as $record) {
                $this->entityManager->remove($record);
            }
            $survey->setCleared();
        }

        $this->entityManager->persist($survey);
        $this->entityManager->flush();
    }
}
