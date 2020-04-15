<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Mvo\ContaoSurvey\Entity\Question;
use Mvo\ContaoSurvey\Registry;
use Mvo\ContaoSurvey\Repository\QuestionRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Terminal42\ServiceAnnotationBundle\ServiceAnnotationInterface;
use Twig\Environment;

class SurveyQuestion implements ServiceAnnotationInterface
{
    private QuestionRepository $questionRepository;
    private Registry $registry;
    private TranslatorInterface $translator;
    private Environment $twig;

    public function __construct(QuestionRepository $questionRepository, Registry $registry, TranslatorInterface $translator, Environment $twig)
    {
        $this->questionRepository = $questionRepository;
        $this->registry = $registry;
        $this->translator = $translator;
        $this->twig = $twig;
    }

    /**
     * @Callback(table="tl_survey_question", target="list.sorting.child_record")
     */
    public function compileRecord(array $data): string
    {
        // todo: refactor to use inline rendering instead of duplicating logic
        $question = $this->getQuestion((int) $data['id']);

        return $this->twig->render(
            '@MvoContaoSurvey/Backend/survey_question_record.html.twig',
            [
                'question' => $question,
                'type' => $this->registry->getTypeForQuestion($question),
            ],
        );
    }

    /**
     * @Callback(table="tl_survey_question", target="fields.type.options")
     */
    public function listAvailableTypes(): array
    {
        $options = [];

        foreach ($this->registry->getTypes() as $type) {
            $options[$type] = $this->translator->trans(
                'survey_question_type.'.$type, [],
                'contao_survey'
            );
        }

        ksort($options);

        return $options;
    }

    /**
     * @Callback(table="tl_survey_question", target="fields.constraint_expression.save")
     */
    public function updateConstraint(string $expression, DataContainer $dc): string
    {
        if ('' === $expression) {
            return '';
        }

        // todo: implement parsing on save, report exceptions

        return '';
    }

    private function getQuestion(int $id): Question
    {
        /** @var Question|null $question */
        $question = $this->questionRepository->find($id);

        if (null === $question) {
            throw new \RuntimeException('Could not find question.');
        }

        return $question;
    }
}
