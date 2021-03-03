<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Report;

use Mvo\ContaoSurvey\Entity\Survey;

interface ExporterInterface
{
    public static function getName(): string;

    public function getExtension(Survey $survey): string;

    public function getMimeType(Survey $survey): string;

    public function getContent(Survey $survey): string;
}
