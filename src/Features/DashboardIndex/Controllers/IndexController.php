<?php

namespace App\Features\DashboardIndex\Controllers;

use Parsedown;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route(path: "/", methods: Request::METHOD_GET)]
    public function indexPage(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'content' => (new Parsedown())
                ->text(file_get_contents(__DIR__ . '/../../../../README.md')),
        ]);
    }
}
