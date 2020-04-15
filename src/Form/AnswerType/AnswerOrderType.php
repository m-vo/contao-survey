<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Form\AnswerType;

use Mvo\ContaoSurvey\Entity\QuestionOrder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnswerOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var QuestionOrder $question */
        $question = $builder->getData()->getQuestion();

        $fieldOptions = [
            'required' => $question->isMandatory(),
            'constraints' => $question->isMandatory() ? [new NotBlank()] : [],
        ];

        $builder->add('order', TextType::class, $fieldOptions); // todo: HiddenType after testing

        $builder->get('order')
            ->addModelTransformer(
                new CallbackTransformer(
                    static function (?array $order): ?string {
                        // array to csv
                        if (null === $order) {
                            return null;
                        }

                        return implode(',', $order);
                    },
                    static function (?string $order) use ($question): ?array {
                        // csv to array
                        if (null === $order) {
                            return null;
                        }

                        $values = array_map('intval', explode(',', $order ?? ''));
                        $numberOfItems = \count($question->getChoices());

                        // make sure every index is found
                        if ($numberOfItems !== \count(array_intersect($values, range(0, $numberOfItems - 1)))) {
                            throw new TransformationFailedException('Invalid order indices.');
                        }

                        return $values;
                    }
                )
            );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        /** @var QuestionOrder $question */
        $question = $form->getData()->getQuestion();

        $view->vars['order_options'] = $question->getChoices();
    }

    public function getBlockPrefix()
    {
        return 'survey_answer_order';
    }
}
