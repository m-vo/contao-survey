<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

use Mvo\ContaoSurvey\Widget\RangeWidget;

$GLOBALS['BE_MOD']['content']['survey'] = [
    'tables' => ['tl_survey', 'tl_survey_section', 'tl_survey_question', 'tl_survey_question_dependency'],
    'javascript' => ['bundles/mvocontaosurvey/survey_backend.js'],
    'stylesheet' => ['bundles/mvocontaosurvey/survey_backend.css'],
];

$GLOBALS['BE_FFL']['range'] = RangeWidget::class;
