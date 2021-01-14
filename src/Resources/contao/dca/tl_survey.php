<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

$GLOBALS['TL_DCA']['tl_survey'] =
    [
        'config' => [
            'dataContainer' => 'Table',
            'enableVersioning' => true,
        ],
        'list' => [
            'sorting' => [
                'mode' => 1,
                'flag' => 1,
                'fields' => ['title'],
                'panelLayout' => 'search,limit',
            ],
            'label' => [
                'fields' => ['title'],
            ],
            'global_operations' => [],
            'operations' => [
                'sections' => [
                    'href' => 'table=tl_survey_section',
                    'icon' => 'edit.svg',
                ],
                'edit' => [
                    'href' => 'act=edit',
                    'icon' => 'header.svg',
                ],
                'csv_export' => [
                    'icon' => 'bundles/mvocontaosurvey/icons/csv.svg',
                    'route' => 'mvo_survey_export',
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
            'default' => 'title;{details_legend},note_submission,button_label,button_href;{published_legend},published',
        ],
        'subpalettes' => [
        ],
        'fields' => [
            'id' => [],
            'tstamp' => [],
            'title' => [
                'inputType' => 'text',
                'search' => true,
                'eval' => [
                    'unique' => true,
                    'mandatory' => true,
                    'maxlength' => 255,
                    'tl_class' => 'w50',
                ],
            ],
            'note_submission' => [
                'inputType' => 'textarea',
                'eval' => [
                    'rte' => 'tinyMCE',
                    'tl_class' => 'clr',
                ],
            ],
            'button_label' => [
                'inputType' => 'text',
                'eval' => [
                    'tl_class' => 'w50',
                ],
            ],
            'button_href' => [
                'inputType' => 'text',
                'eval' => [
                    'tl_class' => 'w50',
                    'rgxp' => 'url',
                    'decodeEntities' => true,
                    'dcaPicker' => true,
                    'addWizardClass' => false,
                ],
            ],
            'published' => [
                'inputType' => 'checkbox',
                'filter' => true,
                'eval' => [
                    'tl_class' => 'w50',
                ],
                'default' => true,
                'save_callback' => [static fn ($v) => '1' === $v],
                // Keep this for MySQL Strict mode. Otherwise Contao would save an empty string
                'sql' => ['type' => 'boolean', 'default' => false],
            ],
        ],
    ];
