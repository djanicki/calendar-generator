<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TermsControllerTest extends WebTestCase
{
    public function testTermsPageIsAvailable(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/terms');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Terms and Conditions');
        
        // Assert the navigation active class works or the link is present
        self::assertSelectorTextContains('.nav-links a.active', 'Terms and Conditions');
        
        // Assert some terms content
        self::assertSelectorTextContains('#acceptance h2', '1. Acceptance of Terms');
        self::assertSelectorTextContains('#summary h3', 'Plain-language summary (non-binding)');
    }
}
