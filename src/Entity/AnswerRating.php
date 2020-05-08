<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mvo\ContaoSurvey\Report\Data;

/**
 * @ORM\Entity()
 *
 * @property QuestionRating $question
 */
class AnswerRating extends Answer
{
    /**
     * @ORM\Column(name="value_int", type="integer", nullable=true)
     */
    private ?int $rating = null;

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): void
    {
        $this->rating = $rating;
    }

    public function addData(Data $data): void
    {
        $data->setValue($this->getRating());
    }
}
