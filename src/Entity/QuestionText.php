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
class QuestionText extends Question
{
    public const VALIDATION__NONE = 0;
    public const VALIDATION__AGE = 1;

    /**
     * @ORM\Column(name="text_validation", type="integer", options={"unsigned": true})
     */
    private int $validationType = self::VALIDATION__NONE;

    public function getValidationType(): int
    {
        return $this->validationType;
    }
}
