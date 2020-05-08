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

    protected function defineData(Data $data): void
    {
        $data->defineValue(Data::EMPTY_LABEL);
    }
}
