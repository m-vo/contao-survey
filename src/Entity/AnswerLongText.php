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
 * @property QuestionText $question
 */
class AnswerLongText extends Answer
{
    /**
     * @ORM\Column(name="user_value_longtext", type="text", nullable=true)
     */
    private ?string $text = null;

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function exportData(DataContainer $container): void
    {
        $container->setValue($this->getText());
    }
}
