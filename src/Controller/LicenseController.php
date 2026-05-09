<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LicenseController extends AbstractController
{
    #[Route(path: '/license', name: 'app_license', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('license/index.html.twig');
    }
}
