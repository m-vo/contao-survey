<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Entity;

use Contao\StringUtil;
use Doctrine\ORM\Mapping as ORM;
use Mvo\ContaoSurvey\Report\DataContainer;

/**
 * @ORM\Entity()
 */
class QuestionSelect extends Question
{
    public const USER_OPTION_VALUE = -1;

    /**
     * @ORM\Column(name="options", type="blob", length=256, nullable=true)
     *
     * @var resource|null
     */
    private $options;

    /**
     * @ORM\Column(name="add_user_option", type="boolean", options={"default": false})
     */
    private bool $allowUserOption = false;

    /**
     * @ORM\Column(name="user_option_label", options={"default": ""})
     */
    private string $userOptionLabel = '';

    /**
     * @ORM\Column(name="allow_multiple", type="boolean", options={"default": false})
     */
    private bool $allowMultiple = false;

    public function getChoices(): array
    {
        if (null === $this->options) {
            return [];
        }

        rewind($this->options);

        /** @var array $options */
        $options = StringUtil::deserialize(stream_get_contents($this->options), true);

        if ($this->allowUserOption) {
            $options[self::USER_OPTION_VALUE] = 'user.option';
        }

        return array_flip($options);
    }

    public function allowMultiple(): bool
    {
        return $this->allowMultiple;
    }

    public function allowUserOption(): bool
    {
        return $this->allowUserOption;
    }

    public function getUserOptionLabels(): array
    {
        $parts = array_map('trim', explode('%', $this->userOptionLabel, 2));

        return [
            $parts[0], $parts[1] ?? null,
        ];
    }

    protected function defineData(DataContainer $container): void
    {
        $container->defineValueOptions($this->getChoices());
    }
}
