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
     * @ORM\OneToMany(targetEntity="Question", mappedBy="survey")
     *
     * @var Collection<Question>
     */
    private Collection $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

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
}
