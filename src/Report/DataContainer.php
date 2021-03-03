<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Report;

class DataContainer
{
    public const EMPTY_LABEL = '';

    private string $label;

    private ?string $valueLabel = null;

    private ?string $value = null;

    /**
     * @var array<int, string>
     */
    private array $optionLabelsById = [];

    /**
     * @var array<int, bool|string|null>
     */
    private array $optionValuesById = [];

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public function defineValue(string $label): void
    {
        $this->valueLabel = $label;
    }

    public function defineValueOptions(array $labelOptionMap): void
    {
        $this->validateOptionMap($labelOptionMap);

        $this->optionLabelsById = array_flip($labelOptionMap);
    }

    public function setValue($value, ?int $optionId = null): void
    {
        $value = null === $value ? $value : (string) $value;

        if (null !== $optionId) {
            $this->validateOptionId($optionId);

            $this->optionValuesById[$optionId] = $value;

            return;
        }

        if (null === $this->valueLabel) {
            throw new \InvalidArgumentException('No value was defined.');
        }

        $this->value = $value;
    }

    public function markOptions(int ...$optionIds): void
    {
        foreach ($optionIds as $optionId) {
            $this->validateOptionId($optionId);

            if (!isset($this->optionValuesById[$optionId])) {
                $this->optionValuesById[$optionId] = true;
            }
        }
    }

    /**
     * @return array<int, bool|string|null>
     */
    public function getValues(): array
    {
        $values = [];

        foreach (array_keys($this->optionLabelsById) as $id) {
            $values[] = $this->optionValuesById[$id] ?? null;
        }

        if (null !== $this->valueLabel) {
            $values[] = $this->value;
        }

        return $values;
    }

    /**
     * @return array<string>
     */
    public function getValueLabels(): array
    {
        $labels = $this->optionLabelsById;

        if (null !== $this->valueLabel) {
            $labels[] = $this->valueLabel;
        }

        return array_values($labels);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    private function validateOptionId(int $optionId): void
    {
        if (!isset($this->optionLabelsById[$optionId])) {
            throw new \InvalidArgumentException("Unknown option '$optionId'");
        }
    }

    private function validateOptionMap(array $labelOptionMap): void
    {
        foreach ($labelOptionMap as $label => $key) {
            if (!\is_string($label) || !\is_int($key)) {
                throw new \InvalidArgumentException('Options must be key value pairs in the form "label [string] => option key [int]"');
            }
        }
    }
}
