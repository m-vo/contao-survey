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

    /**
     * @var array<SurveyStep>
     */
    private array $steps;

    /**
     * @var array<int,array<string,Answer|null>|null>
     */
    private array $answers;

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
     * @return array<Answer>
     */
    public function getAnswers(): array
    {
        return array_values(
            // flatten and remove skipped
            array_filter(array_merge(...array_filter($this->answers)))
        );
    }

    public function saveCurrentStep($skipStep = false): void
    {
        if (!$skipStep) {
            /** @var array<Answer> $answers */
            $answers = $this->form->getData();
            $detached = [];

            // we're detaching references before storing to avoid unnecessarily
            // large storage sizes
            foreach ($answers as $name => $answer) {
                $answer = clone $answer;
                $answer->detach();
                $detached[$name] = $answer;
            }
        } else {
            $detached = null;
        }

        $this->storeAnswers($this->currentStep, $detached);
    }

    public function reset(): void
    {
        $this->resetStorage();

        // rebind to setup current step
        $this->bind($this->survey);
    }

    /**
     * @return array<Question>
     */
    public function getCurrentQuestions(): array
    {
        return array_values(
            array_map(
                static fn (Answer $answer) => $answer->getQuestion(),
                $this->form->getData()
            )
        );
    }

    public function isFirstStep(): bool
    {
        return 0 === $this->currentStep;
    }

    public function isLastStep(): bool
    {
        return $this->currentStep === $this->totalSteps - 1;
    }

    public function getCurrentStep(): SurveyStep
    {
        return $this->steps[$this->currentStep];
    }

    public function getCurrentStepIndex(bool $zeroIndexed = true): int
    {
        return $this->currentStep + ($zeroIndexed ? 0 : 1);
    }

    public function getTotalSteps(): int
    {
        return \count($this->steps);
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

    public function buildSteps(): void
    {
        $steps = [];

        foreach ($this->survey->getSections() as $section) {
            if ([] === $section->getQuestions()) {
                continue;
            }

            if ($section->groupQuestions()) {
                $steps[] = new SurveyStep($section, $section->getQuestions());
                continue;
            }

            foreach ($section->getQuestions() as $question) {
                $steps[] = new SurveyStep($section, [$question]);
            }
        }

        $this->steps = $steps;
    }

    private function bind(Survey $survey): void
    {
        $this->survey = $survey;
        $this->buildSteps();

        // reset storage if question set has changed
        if (!$this->checkQuestionSetUntouched()) {
            $this->resetStorage();
        }

        // answers given (or skipped) so far
        $this->loadAnswers();

        // get current step
        $totalSteps = $this->getTotalSteps();
        $currentStep = $this->loadStepIndex();

        if (\count($this->answers) > ($totalSteps - 1) || $currentStep > $totalSteps - 1) {
            $this->resetStorage();
            $this->answers = [];
        }

        // reuse previously stored data, fallback to create a new prototype
        if (!isset($this->answers[$currentStep])) {
            $questions = $this->steps[$currentStep]->getQuestions();

            foreach ($questions as $question) {
                $questionName = $question->getName();
                $this->answers[$currentStep][$questionName] = $this->answers[$currentStep][$questionName] ?? $this->createAnswer($question);
            }
        }

        $this->currentStep = $currentStep;
        $this->totalSteps = $totalSteps;

        $this->form = $this->formFactory->create(
            SurveyStepFormType::class,
            $this->answers[$currentStep] ?? [],
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
    private function loadStepIndex(): int
    {
        return $this->storage->get($this->survey->getId().'/step', 0);
    }

    private function storeStep(int $stepIndex): void
    {
        $this->storage->set($this->survey->getId().'/step', $stepIndex);
    }

    /**
     * Retrieve answers from storage grouped by step, fallback to '[]'.
     */
    private function loadAnswers(): void
    {
        // step answers given (or skipped) so far
        /** @var array<int,array<string,Answer|null>|null> $answers */
        $answers = $this->storage->get($this->survey->getId().'/answers', []);

        foreach ($answers as $stepIndex => $stepAnswers) {
            if (null === $stepAnswers) {
                // skipped step
                continue;
            }

            foreach ($stepAnswers as $questionName => $answer) {
                if (null === $answer) {
                    continue;
                }

                // reconstruct referenced questions (they were removed when storing)
                $answer->setQuestion($this->steps[$stepIndex]->getQuestion($questionName));
            }
        }

        $this->answers = $answers;
    }

    /**
     * @param array<Answer|null>|null $answers
     */
    private function storeAnswers(int $step, ?array $answers): void
    {
        $this->storage->set($this->survey->getId().'/answers/'.$step, $answers);
    }

    private function resetStorage(): void
    {
        $this->storage->remove((string) $this->survey->getId());

        // todo: should we completely clear the storage after a certain time?
    }

    private function checkQuestionSetUntouched(): bool
    {
        $hash = md5(implode('|', $this->steps));
        $currentHash = $this->storage->get($this->survey->getId().'/hash');

        if (null === $currentHash) {
            $this->storage->set($this->survey->getId().'/hash', $hash);

            return true;
        }

        return $hash === $currentHash;
    }
}
