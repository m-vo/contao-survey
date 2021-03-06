<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Twig\Environment;

class SurveySection
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Callback(table="tl_survey_section", target="list.sorting.child_record")
     */
    public function compileRecord(array $data): string
    {
        return $this->twig->render(
            '@MvoContaoSurvey/Backend/survey_section_record.html.twig',
            [
                'name' => $data['name'],
            ]
        );
    }
}
