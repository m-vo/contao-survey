<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Controller;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\Template;
use Doctrine\ORM\EntityManager;
use Mvo\ContaoSurvey\Entity\Record;
use Mvo\ContaoSurvey\Entity\Survey;
use Mvo\ContaoSurvey\Form\SurveyManager;
use Mvo\ContaoSurvey\Form\SurveyManagerFactory;
use Mvo\ContaoSurvey\Registry;
use Mvo\ContaoSurvey\Repository\SurveyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement(category="includes")
 */
class SurveyFragment extends AbstractContentElementController
{
    private SurveyRepository $surveyRepository;
    private SurveyManagerFactory $managerFactory;
    private ScopeMatcher $scopeMatcher;
    private EntityManager $entityManager;
    private Registry $registry;

    public function __construct(SurveyRepository $surveyRepository, SurveyManagerFactory $managerFactory, ScopeMatcher $scopeMatcher, EntityManager $entityManager, Registry $registry)
    {
        $this->surveyRepository = $surveyRepository;
        $this->managerFactory = $managerFactory;
        $this->scopeMatcher = $scopeMatcher;
        $this->entityManager = $entityManager;
        $this->registry = $registry;
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        /** @var Survey|null $survey */
        $survey = $this->surveyRepository->find((int) $model->survey);

        if (null === $survey) {
            // return empty response if survey wasn't found
            return new Response();
        }

        $headline = $model->survey_headline ?: $survey->getTitle();

        if ($this->scopeMatcher->isBackendRequest($request)) {
            return $this->render('@MvoContaoSurvey/Backend/survey_content.html.twig', [
                'headline' => $headline,
                'survey' => $survey,
            ]);
        }

        if (!$survey->isFrozen()) {
            return $this->render('@MvoContaoSurvey/_frozen.html.twig', [
                'headline' => $model->survey_headline ?: $survey->getTitle(),
                'survey' => $survey,
                'class' => 'survey survey--frozen',
            ]);
        }

        $manager = ($this->managerFactory)($survey);

        $manager->form->handleRequest($request);

        if ($this->proceedUntilCompleted($manager)) {
            $this->storeRecord($survey, $manager->getAnswers());
            $manager->reset();

            return $this->render('@MvoContaoSurvey/_thanks.html.twig', [
                'headline' => $headline,
                'survey' => $survey,
                'class' => 'survey survey--thanks',
            ]);
        }

        $currentStepIndex = $manager->getCurrentStepIndex(false);
        $currentStep = $manager->getCurrentStep();

        return $this->render('@MvoContaoSurvey/_step.html.twig', [
            // survey
            'headline' => $headline,
            'survey' => $survey,
            'total_steps' => $manager->getTotalSteps(),
            'class' => sprintf('survey survey--id_%d', $survey->getId()),

            // current step
            'current_step' => [
                'index' => $currentStepIndex,
                'is_first' => $manager->isFirstStep(),
                'is_last' => $manager->isLastStep(),
                'form' => $manager->form->createView(),
                'questions' => $currentStep->getQuestions(),
                'section' => $currentStep->getSection(),
                'mandatory' => $currentStep->isMandatory(),
            ],

            // meta
            'registry' => $this->registry,
        ]);
    }

    private function proceedUntilCompleted(SurveyManager $manager): bool
    {
        // form wasn't even submitted
        if (!$manager->form->isSubmitted()) {
            return false;
        }

        // reset (back to the first step)
        if ($manager->form['reset']->isClicked()) {
            $manager->reset();

            return false;
        }

        // back to the previous step
        if (isset($manager->form['previous']) && $manager->form['previous']->isClicked() && $manager->previousStep()) {
            return false;
        }

        // go to next step OR we're finally done
        if ($manager->form->isValid()) {
            $manager->saveCurrentStep();

            return !$manager->nextStep();
        }

        // form contains errors
        return false;
    }

    private function storeRecord(Survey $survey, array $answers): void
    {
        $record = new Record($survey, $answers);

        // todo validate

        $this->entityManager->persist($record);
        $this->entityManager->flush();
    }
}
