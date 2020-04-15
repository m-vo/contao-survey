<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Form\AnswerType;

use Mvo\ContaoSurvey\Entity\QuestionRating;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnswerRatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var QuestionRating $question */
        $question = $builder->getData()->getQuestion();

        $builder->add('rating', RangeType::class, [
            'required' => $question->isMandatory(),
            'constraints' => $question->isMandatory() ? [new NotBlank()] : [],
            'attr' => [
                'min' => 1,
                'max' => $question->getRange(),
                'step' => 1,
            ],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'survey_answer_rating';
    }
}
