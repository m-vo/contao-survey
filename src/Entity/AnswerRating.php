<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mvo\ContaoSurvey\Report\DataContainer;

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

    public function exportData(DataContainer $container): void
    {
        $container->setValue($this->getRating());
    }
}
