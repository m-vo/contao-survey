<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mvo\ContaoSurvey\Entity\Question;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * @return array<Question>
     */
    public function findBefore(Question $question): array
    {
        $qb = $this->createQueryBuilder('sq')
            ->where('sq.sorting < :sorting')
            ->andWhere('sq.timestamp > 0')
            ->setParameter('sorting', $question->getSorting())
            ->orderBy('sq.sorting')
        ;

        return $qb->getQuery()->execute();
    }

    public function isNameAlreadyUsed(string $name, Question $question): bool
    {
        $qb = $this->createQueryBuilder('sq')
            ->select('count(sq.id)')
            ->innerJoin('sq.section', 'section')
            ->innerJoin('section.survey', 'survey')
            ->where('survey.id = :surveyId')
            ->andWhere('sq.name = :name')
            ->andWhere('sq.id != :questionId')
            ->setParameter('surveyId', $question->getSection()->getSurvey()->getId())
            ->setParameter('name', $name)
            ->setParameter('questionId', $question->getId())
        ;

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
