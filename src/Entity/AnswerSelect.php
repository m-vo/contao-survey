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
                ->buildViolation('The user option cannot be blank.')
                ->atPath('user_option')
                ->addViolation();
        }
    }

    /**
     * @return array|int|null
     */
    public function getValues()
    {
        return 1 === \count($this->values ?? []) ?
            $this->values[0] : $this->values;
    }

    /**
     * @param array|int|null $values
     */
    public function setValues($values): void
    {
        $this->values = (array) $values;
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
