<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

use Mvo\ContaoSurvey\Entity\QuestionText;

$GLOBALS['TL_LANG']['tl_survey_question']['header_legend'] = 'Meta-Daten';
$GLOBALS['TL_LANG']['tl_survey_question']['question'] = ['Frage / Titel'];
$GLOBALS['TL_LANG']['tl_survey_question']['name'] = ['Name', 'Ein eindeutiger Bezeichner, der im Export und zur Definition von Abhängigkeiten benutzt wird.'];
$GLOBALS['TL_LANG']['tl_survey_question']['description'] = ['Beschreibung', 'Fügen Sie eine optionale Beschreibung hinzu.'];
$GLOBALS['TL_LANG']['tl_survey_question']['image'] = ['Bild', 'Fügen Sie ein optionales Bild hinzu.'];
$GLOBALS['TL_LANG']['tl_survey_question']['instruction'] = ['Anweisung', 'Fügen Sie eine optionale Anweisung hinzu, wie das Formular ausgefüllt werden soll.'];

$GLOBALS['TL_LANG']['tl_survey_question']['options_legend'] = 'Optionen';
$GLOBALS['TL_LANG']['tl_survey_question']['mandatory'] = ['Verpflichtend', 'Diese Frage muss beantwortet werden.'];

$GLOBALS['TL_LANG']['tl_survey_question']['type_legend'] = 'Fragetypen';
$GLOBALS['TL_LANG']['tl_survey_question']['type'] = ['Fragetyp', 'Wählen Sie einen Fragetyp.'];

$GLOBALS['TL_LANG']['tl_survey_question']['matrix_rows'] = ['Reihen', 'Legen Sie die Themen fest, über die abgestimmt werden soll.'];
$GLOBALS['TL_LANG']['tl_survey_question']['matrix_columns'] = ['Spalten', 'Legen Sie die wählbaren Optionen fest.'];
$GLOBALS['TL_LANG']['tl_survey_question']['options'] = ['Optionen', 'Legen Sie die auswählbaren Optionen fest.'];
$GLOBALS['TL_LANG']['tl_survey_question']['rating_range'] = ['Rating Bereich', 'Definieren Sie den Maximalwert dieses Ratings.'];
$GLOBALS['TL_LANG']['tl_survey_question']['add_user_option'] = ['Benutzer-Option hinzufügen', 'Der Benutzer kann selbst eine Option hinzufügen.'];
$GLOBALS['TL_LANG']['tl_survey_question']['user_option_label'] = ['Bezeichner der Benutzer-Option', 'Fügen Sie der Benutzer-Option einen optionalen Bezeichner hinzu. Zum Umschließen kann \'%\' als Platzhalter für das Eingabefeld verwendet werden.'];
$GLOBALS['TL_LANG']['tl_survey_question']['allow_multiple'] = ['Mehrfachauswahl zulassen', 'Es können mehrere Optionen gewählt werden.'];

$GLOBALS['TL_LANG']['tl_survey_question']['expert_legend'] = 'Experten-Einstellungen';
$GLOBALS['TL_LANG']['tl_survey_question']['constraint_expression'] = ['Constraint Expression', 'Fügen Sie ein Anzeigekriterium in Form einer Expression hinzu.'];

$GLOBALS['TL_LANG']['tl_survey_question']['visibility_legend'] = 'Sichtbarkeit';
$GLOBALS['TL_LANG']['tl_survey_question']['published'] = ['Veröffentlicht', 'Diese Frage im Fragebogen anzeigen.'];
