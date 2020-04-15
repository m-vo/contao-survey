<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Form;

use Mvo\ContaoSurvey\Registry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class SurveyStepFormType extends AbstractType
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var SurveyStepModel $stepModel */
        $stepModel = $builder->getData();

        $answer = $stepModel->getAnswer();

        $builder->add(
            'answer',
            $this->registry->getFormTypeClassForAnswer($answer),
            [
                'constraints' => [
                    new Valid(),
                ],
                'data' => $answer,
            ]
        );

        if (!$options['first_step']) {
            $builder->add('previous', SubmitType::class);
        }

        if ($options['last_step']) {
            $builder->add('next', SubmitType::class, ['label' => 'last']);
        } else {
            $builder->add('next', SubmitType::class);
        }

        $builder->add('reset', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'first_step' => false,
            'last_step' => false,
        ]);
    }
}
