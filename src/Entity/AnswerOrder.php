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
 * @property QuestionOrder $question
 */
class AnswerOrder extends Answer
{
    /**
     * @ORM\Column(name="values_json", type="json", nullable=true)
     *
     * @var array<int, int>|null
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

    public function exportData(DataContainer $container): void
    {
        if (null === $this->order) {
            return;
        }

        foreach ($this->order as $index => $optionId) {
            $position = $index + 1;
            $container->setValue($position, $optionId);
        }
    }
}
