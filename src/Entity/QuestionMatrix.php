<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Entity;

use Contao\StringUtil;
use Doctrine\ORM\Mapping as ORM;
use Mvo\ContaoSurvey\Report\Data;

/**
 * @ORM\Entity()
 */
class QuestionMatrix extends Question
{
    /**
     * @ORM\Column(name="matrix_columns", type="blob", length=256, nullable=true)
     *
     * @var resource|null
     */
    private $columns;

    /**
     * @ORM\Column(name="matrix_rows", type="blob", length=256, nullable=true)
     *
     * @var resource|null
     */
    private $rows;

    public function getChoices(): array
    {
        if (null === $this->columns) {
            return [];
        }

        rewind($this->columns);

        /** @var array $options */
        $options = StringUtil::deserialize(stream_get_contents($this->columns), true);

        return array_flip($options);
    }

    public function getRows(): array
    {
        if (null === $this->rows) {
            return [];
        }

        rewind($this->rows);

        /** @var array $options */
        $options = StringUtil::deserialize(stream_get_contents($this->rows), true);

        return array_flip($options);
    }

    protected function defineData(Data $data): void
    {
        $data->defineValueOptions($this->getRows());
    }
}
