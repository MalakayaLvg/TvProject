<?php

namespace App\Controller;

use App\Entity\Series;
use App\Form\SeriesType;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SeriesController extends AbstractController
{
    #[Route('/series', name: 'app_series')]
    public function index(SeriesRepository $seriesRepository): Response
    {
        $series = $seriesRepository->findAll();

        return $this->render('series/index.html.twig', [

            'series' => $series,
        ]);
    }

    #[Route('/series/show/{id}', name: 'app_show', methods: ['GET'])]
    public function show(Series $series):Response
    {
        return $this->render('series/show.html.twig', [
            'series'=>$series,
        ]);
    }

    #[Route('/series/create', name: 'app_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $manager):Response
    {

        $series = new Series();
        $form =  $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($series);
            $manager->flush();

            return $this->redirectToRoute('app_series', ["id"=>$series->getId()]);
        }

        return $this->render('series/create.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    #[Route('/series/delete/{id}', name: 'app_delete', methods: ['POST'])]
    public function delete(Series $series, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($series);
        $entityManager->flush();

        return $this->redirectToRoute('app_series');
    }

    #[Route('/series/{id}/edit', name: 'app_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Series $series, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_series');
        }

        return $this->render('series/edit.html.twig', [
            'form' => $form->createView(),
            'series' => $series,
        ]);
    }

}