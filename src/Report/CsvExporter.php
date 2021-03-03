<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Report;

use League\Csv\Writer;
use Mvo\ContaoSurvey\Entity\Survey;

class CsvExporter implements ExporterInterface
{
    private DataCollector $dataCollector;

    public function __construct(DataCollector $dataCollector)
    {
        $this->dataCollector = $dataCollector;
    }

    public static function getName(): string
    {
        return 'csv';
    }

    public function getExtension(Survey $survey): string
    {
        return 'csv';
    }

    public function getMimeType(Survey $survey): string
    {
        return 'text/csv';
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
