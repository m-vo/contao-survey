<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\EventListener\DataContainer;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\Input;
use Contao\Message;
use Doctrine\ORM\EntityManagerInterface;
use Mvo\ContaoSurvey\Repository\RecordRepository;
use Mvo\ContaoSurvey\Repository\SurveyRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Terminal42\ServiceAnnotationBundle\ServiceAnnotationInterface;
use Twig\Environment;

class Survey implements ServiceAnnotationInterface
{
    private SurveyRepository $surveyRepository;
    private RecordRepository $recordRepository;
    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private Environment $twig;
    private ContaoFramework $framework;
    private TranslatorInterface $translator;

    public function __construct(SurveyRepository $surveyRepository, RecordRepository $recordRepository, EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, TranslatorInterface $translator, Environment $twig, ContaoFramework $framework)
    {
        $this->surveyRepository = $surveyRepository;
        $this->recordRepository = $recordRepository;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->framework = $framework;
        $this->translator = $translator;
    }

    /**
     * @Callback(table="tl_survey", target="config.onload")
     */
    public function disableFrozenCheckbox(DataContainer $dataContainer): void
    {
        $this->framework->initialize();

        /** @var Adapter<Input> $inputAdapter */
        $inputAdapter = $this->framework->getAdapter(Input::class);

        if ('edit' !== $inputAdapter->get('act')) {
            return;
        }

        $survey = $this->surveyRepository->find($dataContainer->id);

        if (null === $survey || $survey->isFrozen()) {
            return;
        }

        if (!$survey->hasRecords()) {
            return;
        }

        $GLOBALS['TL_DCA']['tl_survey']['fields']['frozen']['eval']['disabled'] = true;

        $this->framework->initialize();

        /** @var Adapter<Message> $message */
        $message = $this->framework->getAdapter(Message::class);
        $message->addInfo($this->translator->trans('tl_survey.frozen_disabled', [], 'contao_tl_survey'));
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
