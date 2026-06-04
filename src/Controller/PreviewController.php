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
    public function __invoke(Request $request): Response
    {
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');

        $month = $request->query->getInt('month', $currentMonth);
        $year = $request->query->getInt('year', $currentYear);
        $firstDay = strtolower($request->query->get('first_day', 'monday'));

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

        $grid = $this->generationService->generateCalendar($year, $month, $firstDay);

        return $this->render('preview/index.html.twig', [
            'grid' => $grid,
        ]);
    }
}
