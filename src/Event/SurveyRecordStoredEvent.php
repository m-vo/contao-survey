<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Event;

use Contao\Model;
use Mvo\ContaoSurvey\Entity\Record;
use Mvo\ContaoSurvey\Entity\Survey;

/**
 * @psalm-immutable
 */
final class SurveyRecordStoredEvent
{
    private Survey $survey;

    private Record $record;

    private Model $context;

    public function __construct(Survey $survey, Record $record, Model $context)
    {
        $this->survey = $survey;
        $this->record = $record;
        $this->context = $context;
    }

    public function getSurvey(): Survey
    {
        return $this->survey;
    }

    public function getRecord(): Record
    {
        return $this->record;
    }

    /**
     * Get the context where the survey was stored. Usually a content element or module model.
     */
    public function getContext(): Model
    {
        return $this->context;
    }
}
