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
    public static function getExtension(): string;

    public static function getMimeType(): string;

    public function getContent(Survey $survey): string;
}
