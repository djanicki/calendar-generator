<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Repository\CalendarRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

final class DownloadController extends AbstractController
{
    public function __construct(
        private readonly CalendarRepositoryInterface $calendarRepository,
        private readonly string $generatedCalendarsDir
    ) {}

    #[Route(path: '/download/{token}', name: 'app_download_page', methods: ['GET'])]
    public function page(string $token): Response
    {
        $calendar = $this->calendarRepository->findByToken($token);

        if ($calendar === null || $calendar->isExpired()) {
            throw $this->createNotFoundException('Calendar not found or has expired.');
        }

        return $this->render('download/index.html.twig', [
            'calendar' => $calendar,
        ]);
    }

    #[Route(path: '/download/{token}/file', name: 'app_download_file', methods: ['GET'])]
    public function downloadFile(string $token): Response
    {
        $calendar = $this->calendarRepository->findByToken($token);

        if ($calendar === null || $calendar->isExpired()) {
            throw $this->createNotFoundException('Calendar not found or has expired.');
        }

        $filePath = $this->generatedCalendarsDir . '/' . $calendar->getGeneratedFile();

        if (!is_file($filePath)) {
            throw $this->createNotFoundException('Calendar file not found.');
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $calendar->getGeneratedFile()
        );

        return $response;
    }
}
