<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Form;

use Mvo\ContaoSurvey\Entity\Survey;
use Mvo\ContaoSurvey\Registry;
use Mvo\ContaoSurvey\Storage\StorageInterface;
use Symfony\Component\Form\FormFactoryInterface;

class SurveyManagerFactory
{
    private FormFactoryInterface $formFactory;
    private Registry $registry;
    private StorageInterface $storage;
    private bool $protectEditing;

    public function __construct(FormFactoryInterface $formFactory, Registry $registry, StorageInterface $storage, bool $protectEditing)
    {
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->storage = $storage;
        $this->protectEditing = $protectEditing;
    }

    public function __invoke(Survey $survey): SurveyManager
    {
        return new SurveyManager(
            $survey,
            $this->formFactory,
            $this->registry,
            $this->storage,
            $this->protectEditing
        );
    }
}
