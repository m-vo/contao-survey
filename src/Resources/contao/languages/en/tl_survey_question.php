<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

use Mvo\ContaoSurvey\Entity\QuestionText;

$GLOBALS['TL_LANG']['tl_survey_question']['header_legend'] = 'Meta data';
$GLOBALS['TL_LANG']['tl_survey_question']['question'] = ['Question / Title'];
$GLOBALS['TL_LANG']['tl_survey_question']['name'] = ['Name', 'A unique field name that is used in reports and when defining dependencies.'];
$GLOBALS['TL_LANG']['tl_survey_question']['description'] = ['Description', 'Add an optional description.'];
$GLOBALS['TL_LANG']['tl_survey_question']['image'] = ['Image', 'Add an optional image.'];
$GLOBALS['TL_LANG']['tl_survey_question']['instruction'] = ['Instruction', 'Add an optional instruction how to fill out the form.'];

$GLOBALS['TL_LANG']['tl_survey_question']['options_legend'] = 'Options';
$GLOBALS['TL_LANG']['tl_survey_question']['mandatory'] = ['Mandatory', 'Select if this question needs to be filled out to continue.'];

$GLOBALS['TL_LANG']['tl_survey_question']['type_legend'] = 'Question type specifics';
$GLOBALS['TL_LANG']['tl_survey_question']['type'] = ['Question type', 'Select a question type.'];

$GLOBALS['TL_LANG']['tl_survey_question']['matrix_rows'] = ['Rows', 'Specify the topics on which to vote.'];
$GLOBALS['TL_LANG']['tl_survey_question']['matrix_columns'] = ['Columns', 'Specify the options that can be selected.'];
$GLOBALS['TL_LANG']['tl_survey_question']['options'] = ['Options', 'Specify a number of selectable options.'];
$GLOBALS['TL_LANG']['tl_survey_question']['rating_range'] = ['Rating range', 'Define the maximum range for this rating.'];
$GLOBALS['TL_LANG']['tl_survey_question']['add_user_option'] = ['Add user option', 'Allow the user to specify an extra option herself.'];
$GLOBALS['TL_LANG']['tl_survey_question']['user_option_label'] = ['User option label', 'Add an optional label to be displayed before the user option. To surround it use \'%\' as a placeholder for the input field.'];
$GLOBALS['TL_LANG']['tl_survey_question']['allow_multiple'] = ['Allow multiple', 'Allow to select multiple options.'];

$GLOBALS['TL_LANG']['tl_survey_question']['expert_legend'] = 'Expert settings';
$GLOBALS['TL_LANG']['tl_survey_question']['constraint_expression'] = ['Constraint expression', 'Add a criteria in form of a constraint expression.'];

$GLOBALS['TL_LANG']['tl_survey_question']['visibility_legend'] = 'Visibility';
$GLOBALS['TL_LANG']['tl_survey_question']['published'] = ['Published', 'Make this question available in the survey.'];
