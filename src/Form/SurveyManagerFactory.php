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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SurveyManagerFactory
{
    private FormFactoryInterface $formFactory;
    private Registry $registry;
    private NamespacedAttributeBag $namespacedAttributeBag;
    private SessionInterface $session;
    private bool $protectEditing;

    public function __construct(FormFactoryInterface $formFactory, Registry $registry, NamespacedAttributeBag $namespacedAttributeBag, SessionInterface $session, bool $protectEditing)
    {
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->namespacedAttributeBag = $namespacedAttributeBag;
        $this->session = $session;
        $this->protectEditing = $protectEditing;
    }

    public function __invoke(Survey $survey): SurveyManager
    {
        return new SurveyManager(
            $survey,
            $this->formFactory,
            $this->registry,
            $this->namespacedAttributeBag,
            $this->session,
            $this->protectEditing
        );
    }
}
