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
 * @property QuestionOrder $question
 */
class AnswerOrder extends Answer
{
    /**
     * @ORM\Column(name="values_text", type="simple_array", nullable=true)
     */
    private ?array $order = null;

    public function getOrder(): ?array
    {
        return $this->order;
    }

    public function setOrder(?array $order): void
    {
        $this->order = $order;
    }
}
