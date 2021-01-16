<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Mvo\ContaoSurvey\Validator\Constraints\CompleteAnswerSet;

/**
 * @ORM\Entity(repositoryClass="Mvo\ContaoSurvey\Repository\RecordRepository")
 * @ORM\Table(name="tl_survey_record")
 */
class Record
{
    /**
     * @ORM\Column(name="id", type="integer", options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="submitted_at", type="datetime")
     */
    private \DateTime $submittedAt;

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="record", cascade={"persist"})
     * @CompleteAnswerSet()
     */
    private Collection $answers;

    /**
     * @ORM\ManyToOne(targetEntity="Mvo\ContaoSurvey\Entity\Survey", inversedBy="records")
     * @ORM\JoinColumn(name="survey", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Survey $survey;

    public function __construct(Survey $survey, array $answers)
    {
        $this->submittedAt = new \DateTime();

        $this->survey = $survey;
        $this->answers = new ArrayCollection($answers);

        /** @var Answer $answer */
        foreach ($answers as $answer) {
            $answer->setRecord($this);
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSubmittedAt(): \DateTime
    {
        return $this->submittedAt;
    }

    /**
     * @return array<Answer>
     */
    public function getAnswers(): array
    {
        return $this->answers->toArray();
    }
}
