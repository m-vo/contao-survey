<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mvo\ContaoSurvey\Report\DataContainer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity()
 *
 * @property QuestionSelect $question
 */
class AnswerSelect extends Answer
{
    /**
     * @ORM\Column(name="values_json", type="json", nullable=true)
     */
    private ?array $values = null;

    /**
     * @ORM\Column(name="user_value_text", type="text", nullable=true)
     * @Assert\Length(max="50")
     */
    private ?string $userOption = null;

    /**
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context, $payload): void
    {
        if (empty($this->values)) {
            return;
        }

        // the user cannot be empty once it is selected
        if (null === $this->userOption && \in_array(QuestionSelect::USER_OPTION_VALUE, $this->values, true)) {
            $context
                ->buildViolation('Your own option cannot be blank.')
                ->atPath('user_option')
                ->addViolation();
        }
    }

    public function getMultiple(): ?array
    {
        if (null === $this->values) {
            return null;
        }

        return array_map(fn ($v) => (int) $v, $this->values);
    }

    public function setMultiple(?array $values): void
    {
        $this->values = $values;
    }

    public function getSingle(): ?int
    {
        if (null === $this->values || !isset($this->values[0])) {
            return null;
        }

        return (int) $this->values[0];
    }

    public function setSingle(?int $value): void
    {
        $this->values = null !== $value ? [$value] : null;
    }

    public function getUserOption(): ?string
    {
        return $this->userOption;
    }

    public function setUserOption(?string $userOption): void
    {
        $this->userOption = $userOption;
    }

    public function exportData(DataContainer $container): void
    {
        if ($this->question->allowUserOption()) {
            $container->setValue($this->getUserOption(), QuestionSelect::USER_OPTION_VALUE);
        }

        if ($this->question->allowMultiple()) {
            $values = $this->getMultiple();

            if (null !== $values && 0 !== \count($values)) {
                $container->markOptions(...$values);
            }

            return;
        }

        if (null !== ($value = $this->getSingle())) {
            $container->markOptions($value);
        }
    }
}
