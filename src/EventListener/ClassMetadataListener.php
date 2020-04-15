<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mvo\ContaoSurvey\Entity\Answer;
use Mvo\ContaoSurvey\Entity\Question;
use Mvo\ContaoSurvey\Registry;

class ClassMetadataListener
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function __invoke(LoadClassMetadataEventArgs $args): void
    {
        $classMetadata = $args->getClassMetadata();

        if (Question::class === $classMetadata->getName()) {
            $this->setupSingleTableInheritance($classMetadata, $this->registry->getQuestionClassMapping());
        }

        if (Answer::class === $classMetadata->getName()) {
            $this->setupSingleTableInheritance($classMetadata, $this->registry->getAnswerClassMapping());
        }
    }

    private function setupSingleTableInheritance(ClassMetadata $classMetadata, array $mapping): void
    {
        $classMetadata->setInheritanceType(ClassMetadata::INHERITANCE_TYPE_SINGLE_TABLE);
        $classMetadata->setDiscriminatorColumn(['name' => 'type', 'type' => 'string']);
        $classMetadata->setDiscriminatorMap($mapping);
    }
}
