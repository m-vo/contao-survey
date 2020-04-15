<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

$GLOBALS['TL_DCA']['tl_content']['palettes']['survey_fragment'] = '{type_legend},type;{content_legend},survey_headline;{include_legend},survey';

$GLOBALS['TL_DCA']['tl_content']['fields']['survey_headline'] = [
    'inputType' => 'text',
    'eval' => [
        'style' => 'max-width: 40em',
        'maxlength' => 255,
    ],
    'sql' => [
        'type' => 'string',
        'default' => '',
    ],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['survey'] = [
    'inputType' => 'select',
    'foreignKey' => 'tl_survey.title',
    'eval' => [
        'tl_class' => 'w50',
        'isAssociative' => true,
        'mandatory' => true,
    ],
    'sql' => [
        'type' => 'integer',
        'unsigned' => true,
        'default' => 0,
    ],
];
