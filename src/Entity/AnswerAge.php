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
class AnswerAge extends Answer
{
    /**
     * @ORM\Column(name="value_int", type="integer", nullable=true)
     */
    private ?int $valueInt = null;

    public function getAge(): ?int
    {
        return $this->valueInt;
    }

    public function setAge(?int $age): void
    {
        $this->valueInt = $age;
    }

    public function exportData(DataContainer $container): void
    {
        $container->setValue($this->getAge());
    }
}
