<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Form\AnswerType;

use Mvo\ContaoSurvey\Entity\QuestionRating;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnswerRatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var QuestionRating $question */
        $question = $builder->getData()->getQuestion();

        $total = $question->getRange();
        $range = range(1, $question->getRange());

        $choices = array_combine(
            array_map(
                static fn (int $i) => "$i / $total",
                $range,
            ),
            $range
        );

        $builder->add('rating', ChoiceType::class, [
            'choices' => $choices,
            'expanded' => true,
            'required' => $question->isMandatory(),
            'constraints' => $question->isMandatory() ? [new NotBlank()] : [],
            // Disable placeholder, so we're not getting additional options
            'placeholder' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'survey_answer_rating';
    }
}
