<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Controller;

use Ausi\SlugGenerator\SlugGenerator;
use Ausi\SlugGenerator\SlugOptions;
use Mvo\ContaoSurvey\Entity\Survey;
use Mvo\ContaoSurvey\Report\CsvExporter;
use Mvo\ContaoSurvey\Repository\SurveyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ExportController extends AbstractController
{
    private Security $security;
    private SurveyRepository $surveyRepository;
    private CsvExporter $csvExporter;
    private SlugGenerator $slugGenerator;

    public function __construct(Security $security, SurveyRepository $surveyRepository, CsvExporter $csvExporter, SlugGenerator $slugGenerator)
    {
        $this->security = $security;
        $this->surveyRepository = $surveyRepository;
        $this->csvExporter = $csvExporter;
        $this->slugGenerator = $slugGenerator;
    }

    /**
     * @Route("_mvo_survey/export/csv/{id}",
     *     name="mvo_survey_export",
     *     defaults={
     *          "_scope" = "backend",
     *          "_token_check" = true,
     *     }
     * )
     */
    public function csvExport(int $id): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }

        $survey = $this->surveyRepository->find($id);

        if (null === $survey) {
            throw $this->createNotFoundException('Survey not found.');
        }

        $filename = sprintf(
            '%s_%s.csv',
            $this->slugGenerator->generate(
                $survey->getTitle(),
                (new SlugOptions())->setValidChars('a-zA-z0-9')->setDelimiter('-')
            ),
            date('y-m-d')
        );

        $content = $this->csvExporter->getContent($survey);

        return new Response($content, Response::HTTP_OK, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
