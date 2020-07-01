<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Form;

use Mvo\ContaoSurvey\Entity\Answer;
use Mvo\ContaoSurvey\Entity\Question;
use Mvo\ContaoSurvey\Entity\Survey;
use Mvo\ContaoSurvey\Registry;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;

class SurveyManager
{
    // The managed form is kept transparent to reduce the error of referencing
    // an old form (on the consumer side) when we are switching steps.
    public FormInterface $form;
    private FormFactoryInterface $formFactory;
    private Registry $registry;
    private NamespacedAttributeBag $storage;

    private Survey $survey;
    private int $currentStep;
    private int $totalSteps;

    public function __construct(Survey $survey, FormFactoryInterface $formFactory, Registry $registry, NamespacedAttributeBag $storage)
    {
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->storage = $storage;

        $this->bind($survey);
    }

    /**
     * @return Answer[]
     */
    public function getAnswers(): array
    {
        // filter out skipped
        return array_filter($this->loadAnswers());
    }

    public function saveCurrentStep($skipStep = false): void
    {
        if (!$skipStep) {
            /** @var Answer $answer */
            $answer = $this->form->getData()->getAnswer();

            // we're detaching references before storing to avoid unnecessarily
            // large storage sizes
            $answer->detach();
        } else {
            $answer = null;
        }

        $this->storeAnswer($this->currentStep, $answer);
    }

    public function reset(): void
    {
        $this->resetStorage();

        // rebind to setup current step
        $this->bind($this->survey);
    }

    public function getCurrentQuestion(): Question
    {
        return $this->form->getData()->getAnswer()->getQuestion();
    }

    public function getCurrentType(): string
    {
        return $this->registry->getTypeForQuestion($this->getCurrentQuestion());
    }

    public function isFirstStep(): bool
    {
        return 0 === $this->currentStep;
    }

    public function isLastStep(): bool
    {
        return $this->currentStep === $this->totalSteps - 1;
    }

    public function getCurrentStep(bool $zeroIndexed = true): int
    {
        return $this->currentStep + ($zeroIndexed ? 0 : 1);
    }

    public function getTotalSteps(): int
    {
        return $this->totalSteps;
    }

    public function nextStep(): bool
    {
        if ($this->isLastStep()) {
            return false;
        }

        ++$this->currentStep;
        $this->storeStep($this->currentStep);

        // rebind to setup next step
        $this->bind($this->survey);

        return true;
    }

    public function previousStep(): bool
    {
        if ($this->isFirstStep()) {
            return false;
        }

        --$this->currentStep;
        $this->storeStep($this->currentStep);

        // rebind to setup previous step
        $this->bind($this->survey);

        return true;
    }

    private function bind(Survey $survey): void
    {
        $this->survey = $survey;
        $questions = $survey->getQuestions();

        // reset storage if question set has changed
        if (!$this->checkQuestionSetUntouched($questions)) {
            $this->resetStorage();
        }

        // answers given (or skipped) so far
        $answers = $this->loadAnswers();

        // get current step
        $totalSteps = \count($questions);
        $currentStep = min(
            $this->loadStep(),
            \count($answers),
            $totalSteps - 1
        );

        // todo: consider calling reset and returning instead if
        //       currentStep was constrained by the stored value

        // reuse previously stored data, fallback to create a new prototype
        $answer = $answers[$currentStep] ?? $this->createAnswer($questions[$currentStep]);

        $this->currentStep = $currentStep;
        $this->totalSteps = $totalSteps;

        $this->form = $this->formFactory->create(
            SurveyStepFormType::class,
            new SurveyStepModel($answer),
            [
                'first_step' => $this->isFirstStep(),
                'last_step' => $this->isLastStep(),
            ]
        );
    }

    private function createAnswer(Question $question): Answer
    {
        $class = $this->registry->getAnswerTypeFromQuestion($question);

        /** @var Answer $answer */
        $answer = new $class();
        $answer->setQuestion($question);

        return $answer;
    }

    /**
     * Retrieve step from storage, fallback to '0'.
     */
    private function loadStep(): int
    {
        return $this->storage->get($this->survey->getId().'/step', 0);
    }

    private function storeStep(int $step): void
    {
        $this->storage->set($this->survey->getId().'/step', $step);
    }

    /**
     * Retrieve answers from storage, fallback to '[]'.
     *
     * @return array<Answer|null>
     */
    private function loadAnswers(): array
    {
        // questions
        $questions = $this->survey->getQuestions();

        // answers given (or skipped) so far
        $answers = $this->storage->get($this->survey->getId().'/answers', []);

        /** @var Answer|null $answer */
        foreach ($answers as $index => $answer) {
            if (null === $answer) {
                // skipped questions
                continue;
            }

            // reconstruct referenced questions (they were removed when storing)
            $answer->setQuestion($questions[$index]);
        }

        return $answers;
    }

    private function storeAnswer(int $step, ?Answer $answer): void
    {
        $this->storage->set($this->survey->getId().'/answers/'.$step, $answer);
    }

    private function resetStorage(): void
    {
        $this->storage->remove((string) $this->survey->getId());

        // todo: should we completely clear the storage after a certain time?
    }

    /**
     * @param array<Question> $questions
     */
    private function checkQuestionSetUntouched(array $questions): bool
    {
        $hash = md5(implode('|', $questions));
        $currentHash = $this->storage->get($this->survey->getId().'/hash');

        if (null === $currentHash) {
            $this->storage->set($this->survey->getId().'/hash', $hash);

            return true;
        }

        return $hash === $currentHash;
    }
}
