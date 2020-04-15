<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\ConstraintExpression;

use Mvo\ContaoSurvey\Entity\Answer;
use Mvo\ContaoSurvey\Entity\Question;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Evaluator
{
    private ExpressionLanguage $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function evaluate(string $expression, array $answers): ?bool
    {
        $values = [];

        /** @var Answer $answer */
        foreach ($answers as $answer) {
            $data = $answer->getConstraintData();

            if (null !== $data) {
                $values[$answer->getQuestion()->getName()] = $data;
            }
        }

        try {
            return true === $this->expressionLanguage->evaluate($expression, $values);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function skipQuestion(Question $question, array $allAnswers): bool
    {
        $constraintExpression = $question->getConstraintExpression();

        if (null === $constraintExpression) {
            return false;
        }

        $predecessors = array_map(
            static fn (Question $question) => $question->getId(),
            array_filter(
                $question->getSurvey()->getQuestions(),
                static fn (Question $other) => $other->getSorting() < $question->getSorting()
            )
        );

        // only use answers to preceding questions
        $answers = array_filter(
            $allAnswers,
            static fn (Answer $answer) => \in_array($answer->getQuestion()->getId(), $predecessors, true)
        );

        $showQuestion = $this->evaluate($constraintExpression, $answers) ?? true;

        return !$showQuestion;
    }
}
