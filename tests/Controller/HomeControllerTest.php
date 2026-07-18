<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    public function testHomePageIsAvailable(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Create Beautiful Calendars in Seconds.');
        self::assertSelectorTextContains('.nav-brand', 'Calendar Generator');
        
        // Assert translation of top navigation links
        self::assertSelectorTextContains('.nav-links a[href="/"]', 'Home');
        self::assertSelectorTextContains('.nav-links a[href="/about"]', 'About');
        self::assertSelectorTextContains('.nav-links a[href="/terms"]', 'Terms and Conditions');
    }
}
