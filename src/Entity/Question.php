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
use Mvo\ContaoSurvey\Report\DataContainer;

/**
 * @ORM\Entity(repositoryClass="Mvo\ContaoSurvey\Repository\QuestionRepository")
 * @ORM\Table(name="tl_survey_question",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="name_unique", columns={"pid","name"})
 *     }
 * )
 *
 * This STI class gets configured dynamically based on the configured survey items,
 * see Mvo\ContaoSurvey\EventListener\ClassMetadataListener for details.
 */
abstract class Question extends DcaDefault
{
    public const TYPE = null;

    /**
     * @ORM\ManyToOne(targetEntity="Section", inversedBy="questions")
     * @ORM\JoinColumn(name="pid", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private Section $section;

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="question")
     *
     * @var Collection<int, Answer>
     */
    private Collection $answers;

    /**
     * @ORM\Column(name="sorting", type="integer", options={"unsigned": true})
     */
    private int $sorting;

    /**
     * @ORM\Column(name="name", options={"default": ""})
     */
    private string $name = '';

    /**
     * @ORM\Column(name="question", options={"default": ""})
     */
    private string $question = '';

    /**
     * @ORM\Column(name="description", type="text", options={"default": ""}, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(name="image", type="binary_string", nullable=true, length=16, options={"fixed": true})
     */
    private ?string $image;

    /**
     * @ORM\Column(name="instruction", options={"default": ""})
     */
    private string $instruction = '';

    /**
     * @ORM\Column(name="mandatory", type="boolean", options={"default": false})
     */
    private bool $mandatory;

    /**
     * @ORM\Column(name="constraint_expression", type="text", options={"default": ""}, nullable=true)
     */
    private ?string $constraintExpression;

    /**
     * @ORM\Column(name="published", type="boolean", options={"default": false})
     */
    private bool $published;

    public function __construct()
    {
        $this->answers = new ArrayCollection();

        if (!\is_string(static::TYPE)) {
            throw new \RuntimeException('Type constant has to be defined as a string');
        }
    }

    public function __toString(): string
    {
        return $this->name.'_'.$this->timestamp;
    }

    public function getSection(): Section
    {
        return $this->section;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function getType(): string
    {
        return static::TYPE;
    }

    public function getSorting(): int
    {
        return $this->sorting;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getImage(): ?string
    {
        if (null === $this->image || empty(trim($this->image))) {
            return null;
        }

        return $this->image;
    }

    public function getInstruction(): string
    {
        return $this->instruction;
    }

    public function isMandatory(): bool
    {
        return $this->mandatory;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }

    public function getConstraintExpression(): ?string
    {
        return $this->constraintExpression;
    }

    public function getDataDefinition(): DataContainer
    {
        $data = new DataContainer($this->name);
        $this->defineData($data);

        return $data;
    }

    abstract protected function defineData(DataContainer $container): void;
}
