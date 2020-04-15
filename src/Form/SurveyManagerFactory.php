<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Form;

use Mvo\ContaoSurvey\Entity\Survey;
use Mvo\ContaoSurvey\Registry;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;

class SurveyManagerFactory
{
    private FormFactoryInterface $formFactory;
    private Registry $registry;
    private NamespacedAttributeBag $namespacedAttributeBag;

    public function __construct(FormFactoryInterface $formFactory, Registry $registry, NamespacedAttributeBag $namespacedAttributeBag)
    {
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->namespacedAttributeBag = $namespacedAttributeBag;
    }

    public function __invoke(Survey $survey): SurveyManager
    {
        return new SurveyManager(
            $survey,
            $this->formFactory,
            $this->registry,
            $this->namespacedAttributeBag
        );
    }
}
