<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Mvo\ContaoSurvey\Entity\Survey as SurveyEntity;
use Mvo\ContaoSurvey\Repository\SectionRepository;
use Mvo\ContaoSurvey\Repository\SurveyRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

class FrozenSurvey
{
    private SurveyRepository $surveyRepository;
    private SectionRepository $sectionRepository;
    private RequestStack $requestStack;
    private TranslatorInterface $translator;
    private Session $session;

    public function __construct(SurveyRepository $surveyRepository, SectionRepository $sectionRepository, RequestStack $requestStack, TranslatorInterface $translator, Session $session)
    {
        $this->surveyRepository = $surveyRepository;
        $this->sectionRepository = $sectionRepository;
        $this->requestStack = $requestStack;
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * @Callback(table="tl_survey_section", target="config.onload")
     */
    public function freezeSection(DataContainer $dataContainer): void
    {
        $survey = $this->surveyRepository->find($this->getParentId());

        if (null === $survey) {
            return;
        }

        $this->freezeDataContainer($survey, 'tl_survey_section');
    }

    /**
     * @Callback(table="tl_survey_question", target="config.onload")
     */
    public function freezeQuestion(DataContainer $dataContainer): void
    {
        $section = $this->sectionRepository->find($this->getParentId());

        if (null === $section) {
            return;
        }

        $this->freezeDataContainer($section->getSurvey(), 'tl_survey_question');
    }

    private function freezeDataContainer(SurveyEntity $survey, string $table): void
    {
        if (!$survey->isFrozen()) {
            return;
        }

        $GLOBALS['TL_DCA'][$table]['config']['notCopyable'] = true;
        $GLOBALS['TL_DCA'][$table]['config']['notCreatable'] = true;
        $GLOBALS['TL_DCA'][$table]['config']['notDeletable'] = true;
        $GLOBALS['TL_DCA'][$table]['config']['notSortable'] = true;
        $GLOBALS['TL_DCA'][$table]['config']['notEditable'] = true;

        unset(
            $GLOBALS['TL_DCA'][$table]['list']['operations']['toggle'],
            $GLOBALS['TL_DCA'][$table]['list']['operations']['edit'],
            $GLOBALS['TL_DCA'][$table]['list']['operations']['delete']
        );

        $this->session->getFlashBag()->add(
            'contao.BE.info',
            $this->translator->trans('MSC.surveyFrozen', [], 'contao_default')
        );
    }

    private function getParentId(): int
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \RuntimeException('Unable to get parent id. No request given.');
        }

        if (!$request->query->has('table')) {
            return (int) $request->query->get('id');
        }

        if (!$request->hasSession()) {
            throw new \RuntimeException('Unable to get parent id. No session given.');
        }

        return (int) $request->getSession()->get('CURRENT_ID');
    }
}
