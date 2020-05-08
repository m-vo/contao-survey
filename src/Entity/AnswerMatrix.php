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
 * @property QuestionMatrix $question
 */
class AnswerMatrix extends Answer
{
    /**
     * @ORM\Column(name="values_text", type="simple_array", nullable=true)
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
        if (null === $this->rowIndices) {
            return null;
        }

        return $this->rowIndices[$index] ?? null;
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
