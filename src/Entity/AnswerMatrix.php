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
 * @property QuestionMatrix $question
 */
class AnswerMatrix extends Answer
{
    /**
     * @ORM\Column(name="values_json", type="json", nullable=true)
     */
    private ?array $rowIndices = null;

    public function __set($name, $value): void
    {
        $index = $this->validateAndGetIndex($name);

        if (null === $value) {
            $this->removeRowIndex($index);
        }

        $this->setRowIndex($index, (int) $value);
    }

    public function __get($name): ?int
    {
        $index = $this->validateAndGetIndex($name);

        return $this->getRowIndex($index);
    }

    public function __isset($name): bool
    {
        $index = $this->validateAndGetIndex($name);

        return null !== $this->getRowIndex($index);
    }

    public function getRowIndex(int $index): ?int
    {
        if (null === $this->rowIndices || !isset($this->rowIndices[$index])) {
            return null;
        }

        return (int) $this->rowIndices[$index];
    }

    public function setRowIndex(int $index, int $value): void
    {
        if (null === $this->rowIndices) {
            $this->rowIndices = [];
        }

        $this->rowIndices[$index] = $value;
    }

    public function removeRowIndex(int $index): void
    {
        if (null === $this->rowIndices) {
            return;
        }

        unset($this->rowIndices[$index]);

        if (empty($this->rowIndices)) {
            $this->rowIndices = null;
        }
    }

    public function exportData(DataContainer $container): void
    {
        if (null === $this->rowIndices) {
            return;
        }

        $choiceLabels = array_flip($this->question->getChoices());

        foreach ($this->rowIndices as $index => $rowIndex) {
            $container->setValue($choiceLabels[(int) $rowIndex] ?? null, $index);
        }
    }

    // allow form fields to access virtual variables 'row_0' .. 'row_n' when bound to this entity

    private function validateAndGetIndex(string $name): int
    {
        if (0 !== strpos($name, 'row_')) {
            throw new \UnexpectedValueException('Cannot access property '.$name);
        }

        [, $index] = explode('_', $name);

        return (int) $index;
    }
}
