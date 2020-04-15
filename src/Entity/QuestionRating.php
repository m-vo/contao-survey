<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class QuestionRating extends Question
{
    /**
     * @ORM\Column(name="rating_range", type="integer", options={"unsigned": true})
     */
    private int $range;

    public function getRange(): int
    {
        return $this->range;
    }
}
