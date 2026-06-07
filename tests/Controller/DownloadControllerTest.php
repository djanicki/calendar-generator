<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Domain\Model\Calendar;
use App\Domain\Repository\CalendarRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

final class DownloadControllerTest extends WebTestCase
{
    public function testDownloadPageNotFoundWithInvalidToken(): void
    {
        $client = static::createClient();
        
        // Non-existent UUID
        $client->request('GET', '/download/' . Uuid::v4()->toRfc4122());
        self::assertResponseStatusCodeSame(404);
    }

    public function testDownloadPageExpired(): void
    {
        $client = static::createClient();
        $container = self::getContainer();
        $repository = $container->get(CalendarRepositoryInterface::class);

        $token = Uuid::v4()->toRfc4122();
        $now = new \DateTimeImmutable();

        $calendar = new Calendar(
            token: $token,
            selMonth: 6,
            selYear: 2026,
            mondayFirst: true,
            generatedFile: 'calendar_6_2026_expired.png',
            expiresAt: $now->modify('-10 minutes'),
            createdAt: $now->modify('-1 hour')
        );

        $repository->save($calendar);

        $client->request('GET', '/download/' . $token);
        self::assertResponseStatusCodeSame(404);
    }

    public function testCalendarGenerationAndDownloadFlow(): void
    {
        $client = static::createClient();
        $container = self::getContainer();
        $repository = $container->get(CalendarRepositoryInterface::class);
        $generatedCalendarsDir = $container->getParameter('kernel.project_dir') . '/' . $_ENV['GENERATED_CALENDARS_DIR'];

        // 1. Post to generate a calendar
        $client->request('POST', '/preview/generate', [
            'month' => 12,
            'year' => 2026,
            'first_day' => 'monday'
        ]);

        self::assertResponseRedirects();
        $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.download-heading', 'Your calendar is ready');
        self::assertSelectorTextContains('.download-subtext', '12/2026');

        // Extract token from request URL
        $url = $client->getRequest()->getUri();
        $urlParts = explode('/', rtrim($url, '/'));
        $token = end($urlParts);

        // Fetch calendar from DB to check it was persisted
        $calendar = $repository->findByToken($token);
        self::assertNotNull($calendar);
        self::assertSame(12, $calendar->getSelMonth());
        self::assertSame(2026, $calendar->getSelYear());
        self::assertTrue($calendar->isMondayFirst());

        // Verify file was generated on disk
        $filePath = $generatedCalendarsDir . '/' . $calendar->getGeneratedFile();
        self::assertFileExists($filePath);

        // 2. Request file download
        $client->request('GET', sprintf('/download/%s/file', $token));
        self::assertResponseIsSuccessful();
        
        $response = $client->getResponse();
        self::assertSame('image/png', $response->headers->get('Content-Type'));
        self::assertStringContainsString('attachment', (string) $response->headers->get('Content-Disposition'));
        self::assertStringContainsString($calendar->getGeneratedFile(), (string) $response->headers->get('Content-Disposition'));

        // Cleanup generated file
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
