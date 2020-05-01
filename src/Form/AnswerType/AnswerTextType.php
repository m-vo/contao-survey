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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThan;
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

        if (QuestionText::VALIDATION__AGE === $question->getValidationType()) {
            $minAge = 3;
            $maxAge = 120;

            $fieldOptions = array_merge($fieldOptions, [
                'constraints' => [
                    new GreaterThan($minAge),
                    new LessThan($maxAge),
                ],
                'property_path' => 'age',
                'attr' => [
                    'min' => $minAge,
                    'max' => $maxAge,
                ],
            ]);

            $builder->add('text', IntegerType::class, $fieldOptions);

            return;
        }

        $builder->add('text', TextType::class, $fieldOptions);
    }

    public function getBlockPrefix()
    {
        return 'survey_answer_text';
    }
}
