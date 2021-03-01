<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Mvo\ContaoSurvey\Repository\SurveyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ClearRecordsController extends AbstractController
{
    private Security $security;

    private SurveyRepository $surveyRepository;

    private EntityManagerInterface $entityManager;

    private TranslatorInterface $translator;

    public function __construct(Security $security, SurveyRepository $surveyRepository, EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->security = $security;
        $this->surveyRepository = $surveyRepository;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @Route("_mvo_survey/records/{id}",
     *     name="mvo_survey_clear_records",
     *     defaults={
     *          "_scope" = "backend",
     *          "_token_check" = false,
     *     },
     *     methods={"GET"}
     * )
     */
    public function clearRecords(int $id, Request $request): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }

        $survey = $this->surveyRepository->find($id);

        if (null === $survey) {
            throw $this->createNotFoundException('Survey not found.');
        }

        $survey->clearRecords();

        $this->entityManager->persist($survey);
        $this->entityManager->flush();

        $this->addFlash('contao.BE.info', $this->translator->trans('MSC.surveyClearRecordsSuccess', ['id' => $id], 'contao_default'));

        return $this->redirectToRoute('contao_backend', ['do' => 'survey', 'ref' => $request->attributes->get('_contao_referer_id')]);
    }
}
