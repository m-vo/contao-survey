<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Report;

use League\Csv\Writer;
use Mvo\ContaoSurvey\Entity\Survey;

class CsvExporter
{
    private DataCollector $dataCollector;

    public function __construct(DataCollector $dataCollector)
    {
        $this->dataCollector = $dataCollector;
    }

    public function getContent(Survey $survey): string
    {
        [$header, $rows] = $this->dataCollector->getAsHeaderAndRows($survey);

        $csv = Writer::createFromString();
        $csv->setOutputBOM(Writer::BOM_UTF8);
        $csv->setDelimiter(';');

        $csv->insertOne($header);
        $csv->insertAll($rows);

        return $csv->getContent();
    }
}
