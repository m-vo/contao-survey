<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Controller;

use Ausi\SlugGenerator\SlugGenerator;
use Ausi\SlugGenerator\SlugOptions;
use Mvo\ContaoSurvey\Report\ExporterInterface;
use Mvo\ContaoSurvey\Repository\SurveyRepository;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ExportRecordsController extends AbstractController
{
    private Security $security;
    private SurveyRepository $surveyRepository;
    private ContainerInterface $exporterLocator;
    private SlugGenerator $slugGenerator;

    public function __construct(Security $security, SurveyRepository $surveyRepository, ContainerInterface $exporterLocator, SlugGenerator $slugGenerator)
    {
        $this->security = $security;
        $this->surveyRepository = $surveyRepository;
        $this->exporterLocator = $exporterLocator;
        $this->slugGenerator = $slugGenerator;
    }

    /**
     * @Route("_mvo_survey/export/{exporterName}/{id}",
     *     name="mvo_survey_export",
     *     defaults={
     *          "exporterName" = "csv",
     *          "_scope" = "backend",
     *          "_token_check" = true,
     *     }
     * )
     */
    public function export(string $exporterName, int $id): Response
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException();
        }

        $survey = $this->surveyRepository->find($id);

        if (null === $survey) {
            throw $this->createNotFoundException('Survey not found.');
        }

        $exporter = $this->getExporter($exporterName);
        $filename = sprintf(
            '%s_%s.%s',
            $this->slugGenerator->generate(
                $survey->getTitle(),
                (new SlugOptions())->setValidChars('a-zA-z0-9')->setDelimiter('-')
            ),
            date('y-m-d'),
            $exporter->getExtension($survey)
        );

        $content = $exporter->getContent($survey);

        return new Response($content, Response::HTTP_OK, [
            'Content-Type' => $exporter->getMimeType($survey),
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function getExporter(string $name): ExporterInterface
    {
        try {
            $exporter = $this->exporterLocator->get($name);
        } catch (NotFoundExceptionInterface $exception) {
            throw new BadRequestHttpException(sprintf('Unsupported exporter  "%s".', $name), $exception);
        }

        if (!$exporter instanceof ExporterInterface) {
            throw new \RuntimeException(sprintf('Registered exporter "%s" is not an instance of "%s"', $name, ExporterInterface::class));
        }

        return $exporter;
    }
}
