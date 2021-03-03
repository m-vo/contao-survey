<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Form\AnswerType;

use Mvo\ContaoSurvey\Entity\QuestionMatrix;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnswerMatrixType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var QuestionMatrix $question */
        $question = $builder->getData()->getQuestion();

        $fieldOptions = [
            'required' => $question->isMandatory(),
            'constraints' => $question->isMandatory() ? [new NotBlank()] : [],
            'expanded' => true,
            'placeholder' => false,
            'choices' => $question->getChoices(),
        ];

        foreach ($question->getRows() as $value) {
            $builder->add('row_'.$value, ChoiceType::class, $fieldOptions);
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        /** @var QuestionMatrix $question */
        $question = $form->getData()->getQuestion();

        $view->vars['matrix_rows'] = array_flip($question->getRows());
        $view->vars['matrix_columns'] = array_flip($question->getChoices());
    }

    public function getBlockPrefix()
    {
        return 'survey_answer_matrix';
    }
}
