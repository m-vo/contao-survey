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
                'edit' => [
                    'href' => 'act=edit',
                    'icon' => 'header.svg',
                ],
                'sections' => [
                    'href' => 'table=tl_survey_section',
                    'icon' => 'bundles/mvocontaosurvey/icons/sections.svg',
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
            'default' => 'title,frozen,store_records;{details_legend},note_submission,button_label,button_href',
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
            'frozen' => [
                'inputType' => 'checkbox',
                'filter' => true,
                'eval' => [
                    'tl_class' => 'w50 m12',
                ],
                'default' => true,
                'save_callback' => [static fn ($v) => '1' === $v],
                // Keep this for MySQL Strict mode. Otherwise Contao would save an empty string
                'sql' => ['type' => 'boolean', 'default' => false],
            ],
            'store_records' => [
                'inputType' => 'checkbox',
                'filter' => true,
                'eval' => [
                    'tl_class' => 'clr w50 m12',
                ],
                'default' => true,
                'save_callback' => [static fn ($v) => '1' === $v],
                // Keep this for MySQL Strict mode. Otherwise Contao would save an empty string
                'sql' => ['type' => 'boolean', 'default' => true],
            ],
        ],
    ];
