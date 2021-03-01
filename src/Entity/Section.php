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

/**
 * @ORM\Entity(repositoryClass="Mvo\ContaoSurvey\Repository\SectionRepository")
 * @ORM\Table(name="tl_survey_section",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="name_unique", columns={"pid","name"})
 *     }
 * )
 */
class Section extends DcaDefault
{
    /**
     * @ORM\ManyToOne(targetEntity="Mvo\ContaoSurvey\Entity\Survey", inversedBy="sections")
     * @ORM\JoinColumn(name="pid", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private Survey $survey;

    /**
     * @ORM\Column(name="sorting", type="integer", options={"unsigned": true})
     */
    private int $sorting = 0;

    /**
     * @ORM\Column(name="name", options={"default": ""})
     */
    private string $name = '';

    /**
     * @ORM\Column(name="title", options={"default": ""})
     */
    private string $title = '';

    /**
     * @ORM\Column(name="description", type="text", options={"default": ""}, nullable=true)
     */
    private ?string $description = '';

    /**
     * @ORM\Column(name="grouped", type="boolean", options={"default": true})
     */
    private bool $grouped = true;

    /**
     * @ORM\OneToMany(targetEntity="Question", mappedBy="section")
     *
     * @var Collection<Question>
     */
    private Collection $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getId().'_'.$this->timestamp;
    }

    public function getSurvey(): Survey
    {
        return $this->survey;
    }

    /**
     * @return Question[]
     */
    public function getQuestions(): array
    {
        $questions = $this->questions->toArray();

        // filter unpublished
        $questions = array_filter(
            $questions,
            static fn (Question $question) => $question->isPublished()
        );

        // apply custom sorting
        usort(
            $questions,
            static fn (Question $a, Question $b) => $a->getSorting() <=> $b->getSorting()
        );

        return array_values($questions);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getSorting(): int
    {
        return $this->sorting;
    }

    public function groupQuestions(): bool
    {
        return $this->grouped;
    }
}
