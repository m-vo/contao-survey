<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\EventListener\DataContainer;

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\Message;
use Mvo\ContaoSurvey\Entity\Question;
use Mvo\ContaoSurvey\Entity\Section;
use Mvo\ContaoSurvey\Registry;
use Mvo\ContaoSurvey\Repository\QuestionRepository;
use Mvo\ContaoSurvey\Repository\SectionRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Terminal42\ServiceAnnotationBundle\ServiceAnnotationInterface;
use Twig\Environment;

class SurveyQuestion implements ServiceAnnotationInterface
{
    private QuestionRepository $questionRepository;
    private SectionRepository  $sectionRepository;
    private Registry           $registry;
    private TranslatorInterface $translator;
    private Environment $twig;
    private ContaoFramework $framework;

    public function __construct(QuestionRepository $questionRepository, SectionRepository $sectionRepository, Registry $registry, TranslatorInterface $translator, Environment $twig, ContaoFramework $framework)
    {
        $this->questionRepository = $questionRepository;
        $this->sectionRepository = $sectionRepository;
        $this->registry = $registry;
        $this->translator = $translator;
        $this->twig = $twig;
        $this->framework = $framework;
    }

    /**
     * @Callback(table="tl_survey_question", target="config.onload")
     */
    public function checkEditRestrictions(): void
    {
        $section = $this->sectionRepository->find(CURRENT_ID);

        if (!$section instanceof Section) {
            return;
        }

        $GLOBALS['TL_DCA']['tl_survey_question']['config']['notCopyable'] = true;
        $GLOBALS['TL_DCA']['tl_survey_question']['config']['notCreatable'] = true;
        $GLOBALS['TL_DCA']['tl_survey_question']['config']['notDeletable'] = true;
        $GLOBALS['TL_DCA']['tl_survey_question']['config']['notSortable'] = true;
        $GLOBALS['TL_DCA']['tl_survey_question']['config']['notEditable'] = true;

        unset(
            $GLOBALS['TL_DCA']['tl_survey_question']['list']['operations']['toggle'],
            $GLOBALS['TL_DCA']['tl_survey_question']['list']['operations']['edit'],
            $GLOBALS['TL_DCA']['tl_survey_question']['list']['operations']['delete']
        );

        /** @var Adapter<Message> */
        $message = $this->framework->getAdapter(Message::class);
        $message->addInfo(
            $this->translator->trans('tl_survey_question.published_survey', [], 'contao_tl_survey_question')
        );
    }

    /**
     * @Callback(table="tl_survey_question", target="list.sorting.child_record")
     */
    public function compileRecord(array $data): string
    {
        return $this->twig->render(
            '@MvoContaoSurvey/Backend/survey_question_record_container.html.twig',
            [
                'id' => (int) $data['id'],
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

    /**
     * @Callback(table="tl_survey_question", target="fields.name.save")
     */
    public function validateName(string $name, DataContainer $dc): string
    {
        $question = $this->getQuestion((int) $dc->id);

        if ($this->questionRepository->isNameAlreadyUsed($name, $question)) {
            throw new \InvalidArgumentException($this->translator->trans('error.duplicate_question_name', ['%name%' => $name], 'MvoContaoSurveyBundle'));
        }

        return $name;
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
