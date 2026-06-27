<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Service\CalendarGenerationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PreviewController extends AbstractController
{
    public function __construct(
        private readonly CalendarGenerationService $generationService
    ) {}

    #[Route(path: '/preview', name: 'app_preview', methods: ['GET'])]
    public function preview(Request $request): Response
    {
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');

        $month = $request->query->getInt('month', $currentMonth);
        $year = $request->query->getInt('year', $currentYear);
        $firstDay = strtolower($request->query->get('first_day', 'monday'));
        $template = strtolower($request->query->get('template', 'modern'));

        // Fallback to valid defaults on invalid query params
        if ($month < 1 || $month > 12) {
            $month = $currentMonth;
        }
        if ($year < 1) {
            $year = $currentYear;
        }
        if ($firstDay !== 'monday' && $firstDay !== 'sunday') {
            $firstDay = 'monday';
        }
        if ($template !== 'modern' && $template !== 'classic') {
            $template = 'modern';
        }

        $grid = $this->generationService->generateCalendar($year, $month, $firstDay);

        return $this->render('preview/index.html.twig', [
            'grid' => $grid,
            'template' => $template,
        ]);
    }

    #[Route(path: '/preview/generate', name: 'app_preview_generate', methods: ['POST'])]
    public function generate(Request $request): Response
    {
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');

        $month = (int) $request->request->get('month', $currentMonth);
        $year = (int) $request->request->get('year', $currentYear);
        $firstDay = strtolower((string) $request->request->get('first_day', 'monday'));
        $template = strtolower((string) $request->request->get('template', 'modern'));

        if ($month < 1 || $month > 12) {
            $month = $currentMonth;
        }
        if ($year < 1) {
            $year = $currentYear;
        }
        if ($firstDay !== 'monday' && $firstDay !== 'sunday') {
            $firstDay = 'monday';
        }
        if ($template !== 'modern' && $template !== 'classic') {
            $template = 'modern';
        }

        $calendar = $this->generationService->generateAndPersist($year, $month, $firstDay, $template);

        return $this->redirectToRoute('app_download_page', ['token' => $calendar->getToken()]);
    }
}
