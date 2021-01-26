<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Mvo\ContaoSurvey\Form\ClearRecordsFormType;
use Mvo\ContaoSurvey\Repository\SurveyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ClearRecordsController extends AbstractController
{
    private Security $security;

    private SurveyRepository $surveyRepository;

    private EntityManagerInterface $entityManager;

    private TranslatorInterface $translator;

    private FormFactoryInterface $formFactory;

    public function __construct(Security $security, SurveyRepository $surveyRepository, EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, TranslatorInterface $translator)
    {
        $this->security = $security;
        $this->surveyRepository = $surveyRepository;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->formFactory = $formFactory;
    }

    /**
     * @Route("_mvo_survey/records/{surveyId}",
     *     name="mvo_survey_clear_records",
     *     defaults={
     *          "_scope" = "backend",
     *          "_token_check" = false,
     *     },
     *     methods={"DELETE"}
     * )
     */
    public function clearRecords(int $surveyId, Request $request): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->formFactory->create(ClearRecordsFormType::class, null, ['surveyId' => $surveyId]);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            throw new BadRequestHttpException();
        }

        $survey = $this->surveyRepository->find($surveyId);

        if (null === $survey) {
            throw $this->createNotFoundException('Survey not found.');
        }

        $survey->clearRecords();

        $this->entityManager->persist($survey);
        $this->entityManager->flush();

        $this->addFlash('contao.BE.info', $this->translator->trans('clear_records.success_message', ['%id%' => $surveyId], 'MvoContaoSurveyBundle'));

        return $this->redirectToRoute('contao_backend', ['do' => 'survey', 'ref' => $request->attributes->get('_contao_referer_id')]);
    }
}
