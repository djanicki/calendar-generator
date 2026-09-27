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

        // Assert language selector
        self::assertSelectorExists('.lang-selector');
        self::assertSelectorTextContains('.lang-selector .lang-option[href="/change-locale/en"]', 'EN');
        self::assertSelectorTextContains('.lang-selector .lang-option[href="/change-locale/pl"]', 'PL');
        self::assertSelectorExists('.lang-selector .lang-option[href="/change-locale/en"].active');
    }

    public function testHomePageWithPolishLocale(): void
    {
        $client = static::createClient();
        $client->setServerParameter('HTTP_ACCEPT_LANGUAGE', 'pl');
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Twórz piękne kalendarze w kilka sekund.');
        self::assertSelectorTextContains('.nav-brand', 'Generator Kalendarza');
        self::assertSelectorTextContains('.nav-links a[href="/"]', 'Strona główna');
        self::assertSelectorTextContains('.nav-links a[href="/about"]', 'O projekcie');
        self::assertSelectorTextContains('.nav-links a[href="/terms"]', 'Regulamin');
        self::assertSelectorExists('.lang-selector .lang-option[href="/change-locale/pl"].active');
    }

    public function testChangeLocaleSwitching(): void
    {
        $client = static::createClient();
        $client->request('GET', '/change-locale/pl');

        self::assertResponseRedirects('/');
        $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Twórz piękne kalendarze w kilka sekund.');
        self::assertSelectorExists('.lang-selector .lang-option[href="/change-locale/pl"].active');

        // Switch back to EN
        $client->request('GET', '/change-locale/en');
        self::assertResponseRedirects('/');
        $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Create Beautiful Calendars in Seconds.');
        self::assertSelectorExists('.lang-selector .lang-option[href="/change-locale/en"].active');
    }
}
