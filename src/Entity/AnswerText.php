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
 *
 * @property QuestionText $question
 */
class AnswerText extends Answer
{
    /**
     * @ORM\Column(name="user_value_text", type="string", nullable=true)
     */
    private ?string $text = null;

    /**
     * @ORM\Column(name="value_int", type="integer", nullable=true)
     */
    private ?int $valueInt = null;

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function getAge(): ?int
    {
        return $this->valueInt;
    }

    public function setAge(?int $age): void
    {
        $this->valueInt = $age;
    }
}
