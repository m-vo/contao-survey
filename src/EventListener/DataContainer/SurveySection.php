<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;

class SurveySection
{
    /**
     * @Callback(table="tl_survey_section", target="list.sorting.child_record")
     */
    public function compileRecord(array $data): string
    {
        return $data['title'];
    }
}
