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
            'ptable' => 'tl_survey',
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
                'edit' => [
                    'href' => 'act=edit',
                    'icon' => 'edit.svg',
                ],
                'delete' => [
                    'href' => 'act=delete',
                    'icon' => 'delete.svg',
                    'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
                ],
                'toggle' => [
                    'attributes' => 'onclick="Backend.getScrollOffset();"',
                    'haste_ajax_operation' => [
                        'field' => 'published',
                        'options' => [
                            [
                                'value' => '0',
                                'icon' => 'invisible.svg',
                            ],
                            [
                                'value' => '1',
                                'icon' => 'visible.svg',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'palettes' => [
            '__selector__' => ['type'],
            'default' => '{header_legend},question,name,description,image,instruction;{options_legend},mandatory;{type_legend},type;{expert_legend:hide},constraint_expression;{visibility_legend},published',
        ],
        'subpalettes' => [
            'type_matrix' => 'matrix_rows,matrix_columns',
            'type_order' => 'options',
            'type_rating' => 'rating_range',
            'type_select' => 'options,add_user_option,allow_multiple',
            'type_text' => 'text_validation',
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
                    'unique' => true, // todo: unique only for all with same pid
                    'mandatory' => true,
                    'nospace' => true,
                    'rgxp' => 'fieldname',
                    'maxlength' => 50,
                    'tl_class' => 'w50',
                ],
            ],
            'question' => [
                'inputType' => 'text',
                'search' => true,
                'eval' => [
                    'mandatory' => true,
                    'maxlength' => 255,
                    'tl_class' => 'w50',
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
            ],
            'type' => [
                'inputType' => 'select',
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
                    'tl_class' => 'w50 m12',
                ],
            ],
            'allow_multiple' => [
                'inputType' => 'checkbox',
                'eval' => [
                    'tl_class' => 'w50 clr',
                ],
            ],
            'text_validation' => [
                'inputType' => 'select',
                'reference' => &$GLOBALS['TL_LANG']['tl_survey_question']['text_validation_'],
                'options' => [
                    \Mvo\ContaoSurvey\Entity\QuestionText::VALIDATION__NONE,
                    \Mvo\ContaoSurvey\Entity\QuestionText::VALIDATION__AGE,
                ],
                'eval' => [
                    'tl_class' => 'w50',
                    'mandatory' => true,
                    'isAssociative' => true,
                ],
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
            ],
        ],
    ];
