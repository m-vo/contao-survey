<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Form\AnswerType;

use Mvo\ContaoSurvey\Entity\QuestionText;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnswerAgeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var QuestionText $question */
        $question = $builder->getData()->getQuestion();
        $minAge = 3;
        $maxAge = 120;

        $constraints = [
            new GreaterThanOrEqual($minAge),
            new LessThanOrEqual($maxAge),
        ];

        if ($question->isMandatory()) {
            $constraints[] = new NotBlank();
        }

        $fieldOptions = [
            'required' => $question->isMandatory(),
            'constraints' => $constraints,
            'property_path' => 'age',
            'attr' => [
                'min' => $minAge,
                'max' => $maxAge,
                'class' => 'type--age',
            ],
            'label' => 'Age',
        ];

        $builder->add('text', IntegerType::class, $fieldOptions);
    }

    public function getBlockPrefix(): string
    {
        return 'survey_answer_age';
    }
}
