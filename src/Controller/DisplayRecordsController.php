<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Controller;

use Mvo\ContaoSurvey\Entity\Question;
use Mvo\ContaoSurvey\Registry;
use Mvo\ContaoSurvey\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class DisplayRecordsController extends AbstractController
{
    private QuestionRepository $questionRepository;
    private Registry $registry;
    private Security $security;

    public function __construct(QuestionRepository $questionRepository, Registry $registry, Security $security)
    {
        $this->questionRepository = $questionRepository;
        $this->registry = $registry;
        $this->security = $security;
    }

    /**
     * @Route("_mvo_survey/question/{id}",
     *     name="mvo_survey_question",
     *     defaults={
     *          "_scope" = "backend",
     *          "_token_check" = true,
     *     }
     * )
     */
    public function question(Request $request, int $id): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }

        $stateParameter = $request->get('state');
        $overwriteState = null !== $stateParameter ? (bool) $stateParameter : null;

        /** @var Question|null $question */
        $question = $this->questionRepository->find($id);

        if (null === $question) {
            throw $this->createNotFoundException('Question not found.');
        }

        if (null !== $overwriteState) {
            // Overwrite published state
            $question = clone $question;
            $question->setPublished($overwriteState);
        }

        return $this->render(
            '@MvoContaoSurvey/Backend/survey_question_record.html.twig',
            [
                'question' => $question,
                'type' => $this->registry->getTypeForQuestion($question),
            ]
        );
    }
}
