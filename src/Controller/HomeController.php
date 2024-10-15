<?php

namespace App\Controller;

use App\Service\RequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(RequestService $service): Response
    {
        $series = $service->getSeries();
        dd($series);
        return $this->render('home/index.html.twig',);
    }
}
