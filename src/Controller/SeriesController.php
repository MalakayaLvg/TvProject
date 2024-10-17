<?php

namespace App\Controller;

use App\Entity\Series;
use App\Entity\Comment;
use App\Form\SeriesType;
use App\Form\CommentType;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SeriesController extends AbstractController
{
    #[Route('admin/series', name: 'app_series_admin')]
    public function indexAdmin(SeriesRepository $seriesRepository): Response
    {
        $series = $seriesRepository->findAll();

        return $this->render('/admin/series/index.html.twig', [
            'series' => $series,
        ]);
    }

    #[Route('admin/series/show/{id}', name: 'app_series_show_admin', methods: ['GET', 'POST'])]
    public function show(Series $series, Request $request, EntityManagerInterface $entityManager): Response
    {

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setSeries($series);
            $comment->setUserComment($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_series_show_admin', ['id' => $series->getId()]);
        }

        return $this->render('/admin/series/show.html.twig', [
            'series' => $series,
            'seasons' => $series->getSeasons(),
            'commentForm' => $commentForm->createView(),
            'comments' => $series->getComments(),
        ]);
    }


    #[Route('admin/series/create', name: 'app_series_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $series = new Series();
        $form =  $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($series);
            $manager->flush();

            return $this->redirectToRoute('app_series_admin', ["id" => $series->getId()]);
        }

        return $this->render('/admin/series/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('admin/series/delete/{id}', name: 'app_series_delete', methods: ['POST'])]
    public function delete(Series $series, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($series);
        $entityManager->flush();

        return $this->redirectToRoute('app_series_admin');
    }

    #[Route('admin/series/{id}/edit', name: 'app_series_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Series $series, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_series_show_admin', ['id' => $series->getId()]);
        }

        return $this->render('/admin/series/edit.html.twig', [
            'form' => $form->createView(),
            'series' => $series,
        ]);
    }
}
