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
use Contao\Message;
use Mvo\ContaoSurvey\Entity\Survey as SurveyEntity;
use Mvo\ContaoSurvey\Repository\SurveyRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class SurveySection
{
    private SurveyRepository $surveyRepository;

    private TranslatorInterface $translator;

    private ContaoFramework $framework;

    public function __construct(SurveyRepository $surveyRepository, TranslatorInterface $translator, ContaoFramework $framework)
    {
        $this->surveyRepository = $surveyRepository;
        $this->framework = $framework;
        $this->translator = $translator;
    }

    /**
     * @Callback(table="tl_survey_section", target="config.onload")
     */
    public function checkEditRestrictions(): void
    {
        $survey = $this->surveyRepository->find(CURRENT_ID);

        if (!$survey instanceof SurveyEntity || !$survey->isPublished()) {
            return;
        }

        $GLOBALS['TL_DCA']['tl_survey_section']['config']['notCopyable'] = true;
        $GLOBALS['TL_DCA']['tl_survey_section']['config']['notCreatable'] = true;
        $GLOBALS['TL_DCA']['tl_survey_section']['config']['notDeletable'] = true;
        $GLOBALS['TL_DCA']['tl_survey_section']['config']['notSortable'] = true;

        $GLOBALS['TL_DCA']['tl_survey_section']['fields']['grouped']['eval']['disabled'] = true;

        unset($GLOBALS['TL_DCA']['tl_survey_section']['list']['operations']['delete']);

        /** @var Adapter<Message> */
        $message = $this->framework->getAdapter(Message::class);
        $message->addInfo(
            $this->translator->trans('tl_survey_section.published_survey', [], 'contao_tl_survey_section')
        );
    }

    /**
     * @Callback(table="tl_survey_section", target="list.sorting.child_record")
     */
    public function compileRecord(array $data): string
    {
        return $data['name'];
    }
}
