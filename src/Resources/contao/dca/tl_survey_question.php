<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

$GLOBALS['TL_DCA']['tl_survey_question'] =
    [
        'config' => [
            'dataContainer' => 'Table',
            'enableVersioning' => true,
            'ptable' => 'tl_survey_section',
        ],
        'list' => [
            'sorting' => [
                'mode' => 4,
                'fields' => ['sorting'],
                'panelLayout' => 'search,limit',
                'headerFields' => ['name', 'title'],
            ],
            'label' => [
                'fields' => [],
            ],
            'global_operations' => [],
            'operations' => [
                'edit' => [
                    'href' => 'act=edit',
                    'icon' => 'edit.svg',
                ],
                'copy'     => [
                    'href'  => 'act=copy',
                    'icon'  => 'copy.svg',
                ],
                'cut'    => [
                    'href'       => 'act=paste&amp;mode=cut',
                    'icon'       => 'cut.svg',
                    'attributes' => 'onclick="Backend.getScrollOffset()"',
                ],
                'delete' => [
                    'href' => 'act=delete',
                    'icon' => 'delete.svg',
                    'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
                ],
                'toggle' => [
                    'icon' => 'visible.svg',
                    'attributes' => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                ],
                'show' => [
                    'href' => 'act=show',
                    'icon' => 'show.svg',
                ],
            ],
        ],
        'palettes' => [
            '__selector__' => ['type', 'add_user_option'],
            'default' => '{header_legend},question,name,description,image,instruction;{options_legend},mandatory;{type_legend},type;{expert_legend:hide},constraint_expression;{visibility_legend},published',
        ],
        'subpalettes' => [
            'type_matrix' => 'matrix_rows,matrix_columns',
            'type_order' => 'options',
            'type_rating' => 'rating_range',
            'type_select' => 'options,allow_multiple,add_user_option',
            'add_user_option' => 'user_option_label',
        ],
        'fields' => [
            'id' => [],
            'pid' => [],
            'sorting' => [],
            'tstamp' => [],
            'name' => [
                'inputType' => 'text',
                'search' => true,
                'eval' => [
                    'doNotCopy' => true,
                    'nospace' => true,
                    'rgxp' => 'fieldname',
                    'maxlength' => 255,
                    'tl_class' => 'clr long',
                ],
            ],
            'question' => [
                'inputType' => 'text',
                'search' => true,
                'eval' => [
                    'mandatory' => true,
                    'maxlength' => 255,
                    'tl_class' => 'clr long',
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
            'image' => [
                'inputType' => 'fileTree',
                'eval' => [
                    'fieldType' => 'radio',
                    'filesOnly' => true,
                    'extensions' => Contao\Config::get('validImageTypes'),
                    'tl_class' => 'clr',
                ],
            ],
            'instruction' => [
                'inputType' => 'text',
                'search' => true,
                'eval' => [
                    'maxlength' => 255,
                ],
            ],
            'mandatory' => [
                'inputType' => 'checkbox',
                'default' => true,
                'save_callback' => [static fn ($v) => '1' === $v],
                // Keep this for MySQL Strict mode. Otherwise Contao would save an empty string
                'sql' => ['type' => 'boolean', 'default' => false],
            ],
            'type' => [
                'inputType' => 'select',
                'default' => '',
                'eval' => [
                    'tl_class' => 'w50',
                    'chosen' => true,
                    'submitOnChange' => true,
                    'includeBlankOption' => true,
                    'mandatory' => true,
                ],
            ],
            'matrix_columns' => [
                'inputType' => 'listWizard',
                'eval' => [
                    'mandatory' => true,
                    'tl_class' => 'w50',
                ],
            ],
            'matrix_rows' => [
                'inputType' => 'listWizard',
                'eval' => [
                    'mandatory' => true,
                    'tl_class' => 'w50',
                ],
            ],
            'rating_range' => [
                'inputType' => 'range',
                'eval' => [
                    'tl_class' => 'w50',
                    'rgxp' => 'natural',
                    'minval' => 2,
                    'maxval' => 10,
                ],
            ],
            'options' => [
                'inputType' => 'listWizard',
                'eval' => [
                    'mandatory' => true,
                    'tl_class' => 'w50 clr',
                ],
            ],
            'add_user_option' => [
                'inputType' => 'checkbox',
                'eval' => [
                    'tl_class' => 'clr w50',
                    'submitOnChange' => true,
                ],
                'save_callback' => [static fn ($v) => '1' === $v],
                // Keep this for MySQL Strict mode. Otherwise Contao would save an empty string
                'sql' => ['type' => 'boolean', 'default' => false],
            ],
            'user_option_label' => [
                'inputType' => 'text',
                'eval' => [
                    'maxlength' => 255,
                    'tl_class' => 'clr',
                ],
            ],
            'allow_multiple' => [
                'inputType' => 'checkbox',
                'eval' => [
                    'tl_class' => 'w50 m12',
                ],
                'save_callback' => [static fn ($v) => '1' === $v],
                // Keep this for MySQL Strict mode. Otherwise Contao would save an empty string
                'sql' => ['type' => 'boolean', 'default' => false],
            ],
            'constraint_expression' => [
                'inputType' => 'text',
                'eval' => [
                    'preserveTags' => true,
                    'disabled' => true, // todo: remove once implemented
                ],
            ],
            'published' => [
                'inputType' => 'checkbox',
                'filter' => true,
                'eval' => [
                    'tl_class' => 'm12',
                ],
                'default' => true,
                'save_callback' => [static fn ($v) => '1' === $v],
                // Keep this for MySQL Strict mode. Otherwise Contao would save an empty string
                'sql' => ['type' => 'boolean', 'default' => false],
            ],
        ],
    ];
