<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PreviewControllerTest extends WebTestCase
{
    public function testPreviewPageLoadsSuccessfully(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/preview?month=6&year=2026&first_day=monday');

        self::assertResponseIsSuccessful();
        
        // Assert header has June and 2026
        self::assertSelectorTextContains('.calendar-header h2', 'June');
        self::assertSelectorTextContains('.calendar-header .calendar-year', '2026');

        // Assert all 7 weekdays are printed
        $headers = $crawler->filter('.calendar-grid-header');
        self::assertCount(7, $headers);
        self::assertSame('Mon', trim($headers->eq(0)->text()));
        self::assertSame('Sun', trim($headers->eq(6)->text()));

        // Assert back button is present and links back to home with the parameters
        $backLink = $crawler->selectLink('Back');
        self::assertCount(1, $backLink);
        
        $href = $backLink->attr('href');
        self::assertStringContainsString('month=6', $href);
        self::assertStringContainsString('year=2026', $href);
        self::assertStringContainsString('first_day=monday', $href);

        // Assert generate button is present
        $downloadButton = $crawler->filter('.preview-actions button:contains("Generate")');
        self::assertCount(1, $downloadButton);
    }

    public function testPreviewPageHandlesInvalidInputsGracefully(): void
    {
        $client = static::createClient();
        
        // request with invalid parameters
        $client->request('GET', '/preview?month=99&year=-5&first_day=invalid');

        self::assertResponseIsSuccessful();
        
        // Should fallback to valid defaults (e.g. current year and month, monday)
        $currentMonthName = date('F');
        $currentYear = date('Y');
        
        self::assertSelectorTextContains('.calendar-header h2', $currentMonthName);
        self::assertSelectorTextContains('.calendar-header .calendar-year', $currentYear);
    }
}
