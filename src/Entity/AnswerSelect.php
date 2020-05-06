<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity()
 */
class AnswerSelect extends Answer
{
    /**
     * @ORM\Column(name="values_text", type="simple_array", nullable=true)
     */
    private ?array $values = null;

    /**
     * @ORM\Column(name="user_value_text", type="string", nullable=true)
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
        return $this->values;
    }

    public function setMultiple(?array $values): void
    {
        $this->values = $values;
    }

    public function getSingle(): ?int
    {
        if (null === $this->values) {
            return null;
        }

        return $this->values[0] ?? null;
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
}
