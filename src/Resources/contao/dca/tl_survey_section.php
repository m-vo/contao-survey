<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

$GLOBALS['TL_DCA']['tl_survey_section'] =
    [
        'config' => [
            'dataContainer' => 'Table',
            'ptable' => 'tl_survey',
            'enableVersioning' => true,
        ],
        'list' => [
            'sorting' => [
                'mode' => 4,
                'fields' => ['sorting'],
                'panelLayout' => 'search,limit',
                'headerFields' => ['title'],
            ],
            'label' => [
                'fields' => [],
            ],
            'global_operations' => [],
            'operations' => [
                'questions' => [
                    'href' => 'table=tl_survey_question',
                    'icon' => 'edit.svg',
                ],
                'edit' => [
                    'href' => 'act=edit',
                    'icon' => 'header.svg',
                ],
                'delete' => [
                    'href' => 'act=delete',
                    'icon' => 'delete.svg',
                    'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
                ],
            ],
        ],
        'palettes' => [
            '__selector__' => [],
            'default' => 'name,grouped;{details_legend},title,description',
        ],
        'subpalettes' => [
        ],
        'fields' => [
            'id' => [],
            'tstamp' => [],
            'name' => [
                'inputType' => 'text',
                'search' => true,
                'eval' => [
                    'mandatory' => true,
                    'maxlength' => 255,
                    'tl_class' => 'w50',
                ],
            ],
            'grouped' => [
                'inputType' => 'checkbox',
                'default' => true,
                'eval' => [
                    'tl_class' => 'w50 m12',
                ],
                'save_callback' => [static fn ($v) => '1' === $v],
                // Keep this for MySQL Strict mode. Otherwise Contao would save an empty string
                'sql' => ['type' => 'boolean', 'default' => false],
            ],
            'title' => [
                'inputType' => 'text',
                'eval' => [
                    'maxlength' => 255,
                ],
            ],
            'description' => [
                'inputType' => 'textarea',
                'eval' => [
                    'rte' => 'tinyMCE',
                    'style' => 'max-height: 100px',
                    'tl_class' => 'clr',
                ],
            ],
        ],
    ];
