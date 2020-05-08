<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Report;

use Mvo\ContaoSurvey\Entity\Question;
use Mvo\ContaoSurvey\Entity\Record;
use Mvo\ContaoSurvey\Entity\Survey;

class DataCollector
{
    public function getAsHeaderAndRows(Survey $survey): array
    {
        $questionsInOrder = $survey->getQuestions();
        $dataDefinitions = $this->getDataDefinitions($questionsInOrder);

        /** @var string[] $headers */
        $headers = $this->createHeaders($dataDefinitions);

        /** @var array<string[]> $rows */
        $rows = array_map(
            fn (Record $record) => $this->formatValues(
                $this->createRow($dataDefinitions, $questionsInOrder, $record)
            ),
            $survey->getRecords()
        );

        return [$headers, $rows];
    }

    private function getDataDefinitions(array $questionsInOrder): array
    {
        $dataDefinitions = [];

        /** @var Question $question */
        foreach ($questionsInOrder as $question) {
            $dataDefinitions[$question->getId()] = $question->getDataDefinition();
        }

        return $dataDefinitions;
    }

    private function createHeaders(array $dataDefinitions): array
    {
        if (empty($dataDefinitions)) {
            return [];
        }

        $headerSets = array_map(
            static function (Data $dataDefinition): array {
                // create an array of column labels for each data definition
                $groupLabel = $dataDefinition->getLabel();

                return array_map(
                    static fn ($valueLabel) => html_entity_decode(
                        sprintf('%s%s', $groupLabel, !empty($valueLabel) ? " ($valueLabel)" : '')
                    ),
                    $dataDefinition->getValueLabels()
                );
            },
            $dataDefinitions
        );

        return array_merge(...$headerSets);
    }

    private function createRow(array $dataDefinitions, array $questionsInOrder, Record $record): array
    {
        if (empty($dataDefinitions)) {
            return [];
        }

        // setup dictionary [question id => answer]
        $answers = $record->getAnswers();
        $answerMap = [];

        foreach ($answers as $answer) {
            $answerMap[$answer->getQuestion()->getId()] = $answer;
        }

        // build row sets
        $rowSets = array_map(
            static function (Question $question) use ($dataDefinitions, $answerMap) {
                $questionId = $question->getId();

                /** @var Data|null $dataDefinition */
                $dataDefinition = $dataDefinitions[$questionId] ?? null;

                if (null === $dataDefinition) {
                    throw new \LogicException('Invalid question - was not mapped.');
                }

                if (isset($answerMap[$questionId])) {
                    $answerMap[$questionId]->addData($dataDefinition);
                }

                return $dataDefinition->getValues();
            },
            $questionsInOrder
        );

        if (empty($rowSets)) {
            return [];
        }

        return array_merge(...$rowSets);
    }

    private function formatValues(array $data, string $nullValue = '', string $trueValue = 'x'): array
    {
        return array_map(
            static function ($value) use ($trueValue, $nullValue): string {
                if (null === $value) {
                    return $nullValue;
                }

                if (true === $value) {
                    return $trueValue;
                }

                return (string) $value;
            },
            $data
        );
    }
}
