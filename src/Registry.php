<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey;

use Mvo\ContaoSurvey\Entity\Answer;
use Mvo\ContaoSurvey\Entity\Question;

class Registry
{
    private array $types = [];

    private array $questionClasses = [];
    private array $answerClasses = [];
    private array $formTypeClasses = [];

    public function add(string $type, string $questionClass, string $answerClass, string $formTypeClass): void
    {
        if (\in_array($type, $this->types, true)) {
            throw new \InvalidArgumentException("Type $type is already registered");
        }

        // will all have the same index and therefore allow finding associated entries
        $this->types[] = $type;
        $this->questionClasses[] = $questionClass;
        $this->answerClasses[] = $answerClass;
        $this->formTypeClasses[] = $formTypeClass;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    // todo: simplify + refactor methods for code reuse

    public function getFormTypeClassForAnswer(Answer $answer): string
    {
        foreach ($this->answerClasses as $index => $class) {
            if (!is_a($answer, $class)) {
                continue;
            }

            return $this->formTypeClasses[$index];
        }

        throw new \InvalidArgumentException('Class of given answer is not a registered survey type.');
    }

    public function getAnswerTypeFromQuestion(Question $question): string
    {
        foreach ($this->questionClasses as $index => $class) {
            if (!is_a($question, $class)) {
                continue;
            }

            return $this->answerClasses[$index];
        }

        throw new \InvalidArgumentException('Class of given question is not a registered survey type.');
    }

    public function getTypeForQuestion(Question $question)
    {
        foreach ($this->questionClasses as $index => $class) {
            if (!is_a($question, $class)) {
                continue;
            }

            return $this->types[$index];
        }

        throw new \InvalidArgumentException('Class of given question is not a registered survey type.');
    }

    public function getQuestionClassMapping(): array
    {
        /** @var array $mapping */
        return array_combine($this->types, $this->questionClasses);
    }

    public function getAnswerClassMapping(): array
    {
        /** @var array $mapping */
        return array_combine($this->types, $this->answerClasses);
    }
}
