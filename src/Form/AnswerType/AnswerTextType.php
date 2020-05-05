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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnswerTextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var QuestionText $question */
        $question = $builder->getData()->getQuestion();

        $fieldOptions = [
            'required' => $question->isMandatory(),
            'constraints' => $question->isMandatory() ? [new NotBlank()] : [],
        ];

        $validationType = $question->getValidationType();

        if (QuestionText::VALIDATION__AGE === $validationType) {
            $minAge = 3;
            $maxAge = 120;

            $fieldOptions = array_merge($fieldOptions, [
                'constraints' => [
                    new GreaterThanOrEqual($minAge),
                    new LessThanOrEqual($maxAge),
                ],
                'property_path' => 'age',
                'attr' => [
                    'min' => $minAge,
                    'max' => $maxAge,
                    'class' => 'type--age',
                ],
                'label' => 'Age',
            ]);

            $builder->add('text', IntegerType::class, $fieldOptions);

            return;
        }

        $fieldOptions = array_merge($fieldOptions, [
            'attr' => [
                'class' => 'type--text',
            ],
            'label' => 'Free text',
        ]);

        $builder->add('text', TextareaType::class, $fieldOptions);
    }

    public function getBlockPrefix()
    {
        return 'survey_answer_text';
    }
}
