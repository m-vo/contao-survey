<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Form;

use Mvo\ContaoSurvey\Entity\Question;
use Mvo\ContaoSurvey\Entity\Section;
use RuntimeException;

class SurveyStep
{
    private Section $section;

    /** @var array<string,Question> */
    private array $questions;

    /**
     * @param array<Question> $questions
     */
    public function __construct(Section $section, array $questions)
    {
        $this->section = $section;

        foreach ($questions as $question) {
            $this->questions[$question->getName()] = $question;
        }
    }

    public function getSection(): Section
    {
        return $this->section;
    }

    public function getQuestions(): array
    {
        return array_values($this->questions);
    }

    public function getQuestion(string $questionName): Question
    {
        if (!isset($this->questions[$questionName])) {
            throw new RuntimeException(sprintf('Question named "%s" does not belong to the step', $questionName));
        }

        return $this->questions[$questionName];
    }

    public function __toString(): string
    {
        return $this->section.'.'.implode('|', $this->getQuestions());
    }
}
