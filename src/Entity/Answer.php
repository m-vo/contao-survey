<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Mvo\ContaoSurvey\Repository\AnswerRepository")
 * @ORM\Table(name="tl_survey_answer")
 *
 * This STI class gets configured dynamically based on the configured survey items,
 * see Mvo\ContaoSurvey\EventListener\ClassMetadataListener for details.
 */
abstract class Answer
{
    /**
     * @ORM\Column(name="id", type="integer", options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="answers")
     * @ORM\JoinColumn(name="question", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected ?Question $question;

    /**
     * @ORM\ManyToOne(targetEntity="Mvo\ContaoSurvey\Entity\Record", inversedBy="answers")
     * @ORM\JoinColumn(name="record", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected ?Record $record;

    public function setQuestion(Question $question): void
    {
        $this->question = $question;
    }

    public function getQuestion(): Question
    {
        if (null === $this->question) {
            throw new \RuntimeException('Question was detached or never associated.');
        }

        return $this->question;
    }

    public function setRecord(Record $record): void
    {
        $this->record = $record;
    }

    public function getRecord(): Record
    {
        if (null === $this->record) {
            throw new \RuntimeException('Record was detached or never associated.');
        }

        return $this->record;
    }

    public function getConstraintData(): ?array
    {
        /*
         *  Return a set of variables or test functions (which evaluate to bool)
         *  in your implementation. The data will be available under the name of
         *  the associated question in the dependency constraint.
         *
         * Example:
         *      return [
         *           'value' => $this->value,
         *           'min' => self::VALUE_MINIMUM,
         *           'greater_than' => fn($v) = $this->value > $v,
         *      ];
         *
         *  can be used in a question named 'size' like this:
         *    'size.value != 5 and size.greater_than(size.min + 1)'
         */

        return null;
    }

    /**
     * @internal
     *
     * Detach references. Do not call this on a managed entity. We're using this to
     * simplify storing our entity to the session without the need of explicit
     * serialization.
     */
    public function detach(): void
    {
        $this->question = null;
        $this->record = null;
    }
}
