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
class QuestionOrder extends Question
{
    public const TYPE = 'order';

    /**
     * @ORM\Column(name="options", type="blob", length=256, nullable=true)
     *
     * @var resource|null
     */
    private $options;

    public function getChoices(): array
    {
        if (null === $this->options) {
            return [];
        }

        rewind($this->options);

        /** @var array $options */
        $options = StringUtil::deserialize(stream_get_contents($this->options), true);

        return array_flip($options);
    }

    protected function defineData(DataContainer $container): void
    {
        $container->defineValueOptions($this->getChoices());
    }
}
