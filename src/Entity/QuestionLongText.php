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
 */
class QuestionLongText extends Question
{
    public const TYPE = 'longtext';

    protected function defineData(DataContainer $container): void
    {
        $container->defineValue(DataContainer::EMPTY_LABEL);
    }
}
