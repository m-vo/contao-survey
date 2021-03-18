<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\EventListener\DataContainer;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\Input;
use Mvo\ContaoSurvey\Repository\RecordRepository;
use Mvo\ContaoSurvey\Repository\SurveyRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class Survey
{
    private SurveyRepository $surveyRepository;
    private RecordRepository $recordRepository;
    private ContaoFramework $framework;
    private TranslatorInterface $translator;
    private Session $session;
    private Environment $twig;
    private bool $protectEditing;

    public function __construct(SurveyRepository $surveyRepository, RecordRepository $recordRepository, ContaoFramework $framework, TranslatorInterface $translator, Session $session, Environment $twig, bool $protectEditing)
    {
        $this->surveyRepository = $surveyRepository;
        $this->recordRepository = $recordRepository;
        $this->framework = $framework;
        $this->translator = $translator;
        $this->session = $session;
        $this->twig = $twig;
        $this->protectEditing = $protectEditing;
    }

    /**
     * @Callback(table="tl_survey", target="config.onload")
     */
    public function frozenCheckbox(DataContainer $dataContainer): void
    {
        if (!$this->protectEditing) {
            PaletteManipulator::create()
                ->removeField('frozen')
                ->applyToPalette('default', 'tl_survey')
            ;

            return;
        }

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

        $this->session->getFlashBag()->add(
            'contao.BE.info',
            $this->translator->trans('frozen.disabled', [], 'MvoContaoSurveyBundle')
        );
    }

    /**
     * @Callback(table="tl_survey", target="list.label.label")
     */
    public function listSurveys(array $row, string $label): string
    {
        $submittedRecordsCount = $this->recordRepository->countBySurveyId((int) $row['id']);

        return $this->twig->render(
            '@MvoContaoSurvey/Backend/survey_record.html.twig',
            [
                'id' => (int) $row['id'],
                'name' => $label,
                'count' => $submittedRecordsCount,
                'frozen' => (bool) $row['frozen'],
                'protect_editing' => $this->protectEditing,
                'store_records' => (bool) $row['store_records'],
            ]
        );
    }
}
