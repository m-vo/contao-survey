<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\EventListener\DataContainer;

use Ausi\SlugGenerator\SlugGeneratorInterface;
use Contao\BackendUser;
use Contao\Controller;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Mvo\ContaoSurvey\Entity\Question;
use Mvo\ContaoSurvey\Registry;
use Mvo\ContaoSurvey\Repository\QuestionRepository;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class SurveyQuestion
{
    private QuestionRepository $questionRepository;
    private Registry $registry;
    private EntityManagerInterface $entityManager;
    private TranslatorInterface $translator;
    private Environment $twig;
    private SlugGeneratorInterface $slugGenerator;
    private Connection $connection;

    public function __construct(QuestionRepository $questionRepository, EntityManagerInterface $entityManager, Registry $registry, TranslatorInterface $translator, Environment $twig, SlugGeneratorInterface $slugGenerator, Connection $connection)
    {
        $this->questionRepository = $questionRepository;
        $this->entityManager = $entityManager;
        $this->registry = $registry;
        $this->translator = $translator;
        $this->twig = $twig;
        $this->slugGenerator = $slugGenerator;
        $this->connection = $connection;
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
     * @Callback(table="tl_survey_question", target="list.operations.toggle.button")
     */
    public function togglePublishedStateButton($row, $href, $label, $title, $icon, $attributes): string
    {
        if (null === ($tid = Input::get('tid'))) {
            // Render the button
            $href .= sprintf('&amp;tid=%s&amp;state=%s', $row['id'], $row['published']);

            if (!$row['published']) {
                $icon = 'invisible.svg';
            }

            return sprintf(
                '<a href="%s" title="%s"%s>%s</a> ',
                Controller::addToUrl($href),
                StringUtil::specialchars($title),
                $attributes,
                Image::getHtml($icon, $label, sprintf('data-state="%d"', $row['published'] ? 1 : 0))
            );
        }

        // Toggle published state
        /** @var BackendUser $user */
        $user = BackendUser::getInstance();

        if (!$user->hasAccess('tl_survey_question::published', 'alexf')) {
            throw new AccessDeniedException("Not enough permissions to publish/unpublish survey question ID {$tid}.");
        }

        if (null === ($question = $this->questionRepository->find($tid))) {
            throw new \RuntimeException('Could not find question.');
        }

        $question->setPublished('1' === Input::get('state'));
        $this->entityManager->flush();

        return '';
    }

    /**
     * @Callback(table="tl_survey_question", target="fields.type.options")
     */
    public function listAvailableTypes(): array
    {
        $options = [];

        foreach ($this->registry->getTypes() as $type) {
            $options[$type] = $this->translator->trans(
                'survey_question_type.'.$type,
                [],
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
    public function generateAndValidateName(string $name, DataContainer $dc): string
    {
        $question = $this->getQuestion((int) $dc->id);
        $maxlength = $this->getMaxLengthOfNameField();

        if ('' === $name) {
            $name = $this->generateName($question, $maxlength);
        }

        $this->validateName($name, $question);

        return $name;
    }

    private function generateName(Question $question, int $maxlength): string
    {
        $name = $this->slugGenerator->generate($question->getQuestion());
        $base = $name;

        // avoid collisions
        for ($suffix = 2; $this->questionRepository->isNameAlreadyUsed($name, $question); ++$suffix) {
            $name = substr($base, 0, $maxlength - \strlen((string) $suffix) - 1)."-$suffix";
        }

        return $name;
    }

    private function validateName(string $name, Question $question): void
    {
        if ($this->questionRepository->isNameAlreadyUsed($name, $question)) {
            throw new \InvalidArgumentException($this->translator->trans('error.duplicate_question_name', ['%name%' => $name], 'MvoContaoSurveyBundle'));
        }
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

    private function getMaxLengthOfNameField(): int
    {
        $columns = $this->connection->getSchemaManager()->listTableColumns('tl_survey_question');

        if (!isset($columns['name'])) {
            throw new \RuntimeException('Column "name" does not exist.');
        }

        $length = $columns['name']->getLength();

        if (null === $length) {
            throw new \RuntimeException('Could not determine length of field "name".');
        }

        return $length;
    }
}
