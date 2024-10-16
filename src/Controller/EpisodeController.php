<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EpisodeController extends AbstractController
{

    #[Route('/episode/show/{id}', name: 'app_episode_show', methods: ['GET'])]
    public function show(Episode $episode): Response
    {
        return $this->render('/admin/episode/show.html.twig', [
            'episode' => $episode,
        ]);
    }

    #[Route('/episode/create', name: 'app_episode_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($episode);
            $entityManager->flush();

            return $this->redirectToRoute('app_season_show', ['id' => $episode->getSeason()->getId()]);
        }

        return $this->render('/admin/episode/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/episode/delete/{id}', name: 'app_episode_delete', methods: ['POST'])]
    public function delete(Episode $episode, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($episode);
        $entityManager->flush();

        return $this->redirectToRoute('app_season_show', ['id' => $episode->getSeason()->getId()]);
    }

    #[Route('/episode/{id}/edit', name: 'app_episode_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Episode $episode, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_season_show', ['id' => $episode->getSeason()->getId()]);
        }

        return $this->render('/admin/episode/edit.html.twig', [
            'form' => $form->createView(),
            'episode' => $episode,
        ]);
    }
}
