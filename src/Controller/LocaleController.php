<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LocaleController extends AbstractController
{
    private const array SUPPORTED_LOCALES = ['en', 'pl'];

    #[Route(path: '/change-locale/{locale}', name: 'app_change_locale', methods: ['GET'])]
    public function changeLocale(string $locale, Request $request): Response
    {
        $locale = strtolower($locale);
        if (in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $request->getSession()->set('_locale', $locale);
        }

        $referer = $request->headers->get('referer');
        if ($referer !== null && filter_var($referer, FILTER_VALIDATE_URL) !== false) {
            $refererHost = parse_url($referer, PHP_URL_HOST);
            $currentHost = $request->getHost();

            if ($refererHost === $currentHost) {
                return $this->redirect($referer);
            }
        }

        return $this->redirectToRoute('app_home');
    }
}
