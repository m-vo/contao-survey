<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use Mvo\ContaoSurvey\ConstraintExpression\Evaluator;
use Mvo\ContaoSurvey\Entity\Answer;
use Mvo\ContaoSurvey\Entity\Question;
use Mvo\ContaoSurvey\Entity\Survey;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class CompleteAnswerSetValidator extends ConstraintValidator
{
    private Evaluator $constraintExpressionEvaluator;

    public function __construct(Evaluator $constraintExpressionEvaluator)
    {
        $this->constraintExpressionEvaluator = $constraintExpressionEvaluator;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CompleteAnswerSet) {
            throw new UnexpectedTypeException($constraint, CompleteAnswerSet::class);
        }

        if (empty($value)) {
            return;
        }

        if ($value instanceof ArrayCollection) {
            $value = $value->toArray();
        } elseif (!\is_array($value)) {
            throw new UnexpectedValueException($value, 'array|ArrayCollection');
        }

        foreach ($value as $answer) {
            if (!$answer instanceof Answer) {
                throw new UnexpectedValueException($value, 'array of Entity\Answer');
            }
        }

        $this->validateSurvey($value);
        $this->validateAnswers($value);
    }

    private function validateSurvey(array $answers): void
    {
        $questions = array_map(
            static fn (Answer $answer) => $answer->getQuestion(),
            $answers
        );

        /** @var array<Survey> $surveys */
        $surveys = array_unique(
            array_map(
                static fn (Question $question) => $question->getSection()->getSurvey(),
                $questions
            )
        );

        if (1 !== \count($surveys)) {
            $this->context
                ->buildViolation('Associated survey of related questions must match.')
                ->addViolation()
            ;
        }
    }

    private function validateAnswers(array $answers): void
    {
        /** @var Survey $survey */
        $survey = $answers[0]->getQuestion()->getSurvey();

        $surveyQuestionIds = array_map(
            static fn (Question $question) => $question->getId(),
            $survey->getQuestions()
        );

        $availableQuestionIds = array_map(
            static fn (Answer $answer) => $answer->getQuestion()->getId(),
            $answers
        );

        // unique questions
        if (\count($availableQuestionIds) !== \count(array_unique($availableQuestionIds))) {
            $this->context
                ->buildViolation('There cannot be multiple answers to a single question in the result set.')
                ->addViolation()
            ;
        }

        /** @var Question $question */
        foreach ($survey->getQuestions() as $question) {
            $skip = $this->constraintExpressionEvaluator->skipQuestion($question, $answers);
            $available = \in_array($question->getId(), $availableQuestionIds, true);

            // answer missing
            if (!$skip && !$available) {
                $this->context
                    ->buildViolation("Answer to question '{$question->getName()}' must be in the result set.")
                    ->addViolation()
                ;
            }

            // skipped but still in set
            if ($skip && $available) {
                $this->context
                    ->buildViolation("Answer to skipped question '{$question->getName()}' cannot be in the result set.")
                    ->addViolation()
                ;
            }
        }

        // additional answers
        if (!empty(array_diff($availableQuestionIds, $surveyQuestionIds))) {
            $this->context
                ->buildViolation('Additional answers cannot be in the result set.')
                ->addViolation()
            ;
        }
    }
}
