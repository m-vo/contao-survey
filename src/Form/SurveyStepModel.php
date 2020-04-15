<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Form;

use Mvo\ContaoSurvey\Entity\Answer;
use Symfony\Component\Validator\Constraints as Assert;

class SurveyStepModel
{
    /**
     * @Assert\Valid()
     */
    private Answer $answer;

    public function __construct(Answer $answer)
    {
        $this->answer = $answer;
    }

    public function setAnswer(Answer $answer): void
    {
        $this->answer = $answer;
    }

    public function getAnswer(): Answer
    {
        return $this->answer;
    }
}
