<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Entity;

use function array_merge;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="tl_survey")
 */
class Survey extends DcaDefault
{
    /**
     * @ORM\Column(name="title", options={"default": ""})
     */
    private string $title = '';

    /**
     * @ORM\Column(name="note_submission", type="text", options={"default": ""}, nullable=true)
     */
    private ?string $noteAfterSubmission;

    /**
     * @ORM\Column(name="button_href", options={"default": ""})
     */
    private string $buttonHref = '';

    /**
     * @ORM\Column(name="button_label", options={"default": ""})
     */
    private string $buttonLabel = '';

    /**
     * @ORM\OneToMany(targetEntity="Section", mappedBy="survey")
     *
     * @var Collection<Section>
     */
    private Collection $sections;

    /**
     * @ORM\OneToMany(targetEntity="Mvo\ContaoSurvey\Entity\Record", mappedBy="survey")
     *
     * @var Collection<Question>
     */
    private Collection $records;

    /**
     * @ORM\Column(name="published", type="boolean", options={"default": false})
     */
    private bool $published;

    /**
     * @ORM\Column(name="cleared", type="boolean", options={"default": false})
     */
    private bool $cleared;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->records = new ArrayCollection();
    }

    /**
     * @return Section[]
     */
    public function getSections(): array
    {
        $questions = $this->sections->toArray();

        // apply custom sorting
        usort(
            $questions,
            static fn (Section $a, Section $b) => $a->getSorting() <=> $b->getSorting()
        );

        return array_values($questions);
    }

    /**
     * @return array<Question>
     */
    public function getQuestions(): array
    {
        $sectionsInOrder = $this->getSections();
        $questionsInOrder = [];

        foreach ($sectionsInOrder as $section) {
            $questionsInOrder[] = $section->getQuestions();
        }

        if ([] === $questionsInOrder) {
            return [];
        }

        return array_merge(...$questionsInOrder);
    }

    /**
     * @return Record[]
     */
    public function getRecords(): array
    {
        return $this->records->toArray();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getNoteAfterSubmission(): ?string
    {
        return $this->noteAfterSubmission;
    }

    public function getButtonHref(): string
    {
        return $this->buttonHref ?: '/';
    }

    public function getButtonLabel(): string
    {
        return $this->buttonLabel;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function isCleared(): bool
    {
        return $this->cleared;
    }

    public function resetCleared(): void
    {
        $this->cleared = false;
    }

    public function setCleared(): void
    {
        $this->cleared = true;
    }
}
