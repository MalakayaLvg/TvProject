<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Series;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SeasonController extends AbstractController
{
    #[Route('/season/show/{id}', name: 'app_season_show', methods: ['GET'])]
    public function show(Season $season): Response
    {
        return $this->render('/admin/season/show.html.twig', [
            'season' => $season,
        ]);
    }

    #[Route('admin/season/show/{id}', name: 'app_season_show', methods: ['GET'])]
    public function showAdmin(Season $season): Response
    {
        return $this->render('/admin/season/show.html.twig', [
            'season' => $season,
        ]);
    }


    #[Route('/admin/season/create/{series}', name: 'app_season_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $manager, Series $series): Response
    {
        $season = new Season();
        $season->setSeries($series);
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($season);
            $manager->flush();

            return $this->redirectToRoute('app_series_show_admin', ['id' => $series->getId()]);
        }

        return $this->render('/admin/season/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('admin/season/{id}', name: 'app_season_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Season $season, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $season->getId(), $request->request->get('_token'))) {
            $series = $season->getSeries();

            $entityManager->remove($season);
            $entityManager->flush();

            if ($series) {
                return $this->redirectToRoute('app_series_show_admin', ['id' => $series->getId()]);
            }
        }
        return $this->redirectToRoute('app_season_admin');
    }


    #[Route('admin/season/{id}/edit', name: 'app_season_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, season $season, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_season_show', ['id' => $season->getId()] );
        }

        return $this->render('/admin/season/edit.html.twig', [
            'form' => $form->createView(),
            'season' => $season,
        ]);
    }

}