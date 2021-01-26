<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ClearRecordsFormType extends AbstractType
{
    private RouterInterface $router;

    private TranslatorInterface $translator;

    public function __construct(RouterInterface $router, TranslatorInterface $translator)
    {
        $this->router = $router;
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'csrf_protection' => true,
                'csrf_field_name' => '_token',
                'csrf_token_id' => 'mvo_survey_clear_records',
                'translation_domain' => 'MvoContaoSurveyBundle',
                'attr' => ['class' => 'clear-records-form'],
            ]
        );

        $resolver->setRequired('surveyId');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod('DELETE');
        $builder->setAction($this->router->generate('mvo_survey_clear_records', ['surveyId' => $options['surveyId']]));

        $builder->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'clear_records.submit_button',
                'attr' => [
                    'title' => 'clear_records.submit_button',
                    'onclick' => sprintf(
                        'if(!confirm(\'%s\'))return false;Backend.getScrollOffset();return true;',
                        $this->translator->trans('clear_records.confirm', ['%surveyId%' => $options['surveyId']], 'MvoContaoSurveyBundle')
                    ),
                ],
            ]
        );
    }
}
