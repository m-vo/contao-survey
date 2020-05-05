<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Form\AnswerType;

use Mvo\ContaoSurvey\Entity\QuestionSelect;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnswerSelectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var QuestionSelect $question */
        $question = $builder->getData()->getQuestion();

        $builder->add('values', ChoiceType::class, [
            'choices' => $question->getChoices(),
            'multiple' => $question->allowMultiple(),
            'expanded' => true,
            'required' => $question->isMandatory(),
            'constraints' => $question->isMandatory() ? [new NotBlank()] : [],
            'placeholder' => false,
            'property_path' => $question->allowMultiple() ? 'multiple' : 'single',
        ]);

        if ($question->allowUserOption()) {
            $builder->add('user_option', TextType::class, [
                'required' => false,
                'attr' => [
                    'maxlength' => 50,
                ],
            ]);
        }
    }

    public function getBlockPrefix()
    {
        return 'survey_answer_select';
    }
}
