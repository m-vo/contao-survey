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
     * @ORM\Column(name="store_records", type="boolean", options={"default": true})
     */
    private bool $storeRecords = true;

    /**
     * @ORM\OneToMany(targetEntity="Section", mappedBy="survey")
     *
     * @var Collection<int, Section>
     */
    private Collection $sections;

    /**
     * @ORM\OneToMany(targetEntity="Mvo\ContaoSurvey\Entity\Record", mappedBy="survey", orphanRemoval=true)
     *
     * @var Collection<int, Record>
     */
    private Collection $records;

    /**
     * @ORM\Column(name="frozen", type="boolean", options={"default": false})
     */
    private bool $frozen;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->records = new ArrayCollection();
    }

    /**
     * @return array<Section>
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
     * @return array<Record>
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

    public function isFrozen(): bool
    {
        return $this->frozen;
    }

    public function isStoreRecords(): bool
    {
        return $this->storeRecords;
    }

    public function clearRecords(): void
    {
        $this->records->clear();
    }

    public function hasRecords(): bool
    {
        return $this->records->count() > 0;
    }
}
