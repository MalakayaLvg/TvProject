<?php

namespace App\Controller;

use App\Entity\WatchList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Film;
use App\Entity\Series;
use Symfony\Component\HttpFoundation\RedirectResponse;


class WatchListController extends AbstractController
{
    #[Route('/watchlist', name: 'watchlist_index')]
    public function index(EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $watchList = $entityManager->getRepository(WatchList::class)->findOneBy(['watchListUser' => $user]);

        if (!$watchList) {
            $watchList = new WatchList();
            $watchList->setWatchListUser($user);
            $entityManager->persist($watchList);
            $entityManager->flush();
        }

        return $this->render('/client/watch_list/index.html.twig', [
            'watchList' => $watchList,
        ]);
    }

    #[Route('/watchlist/delete-film/{id}', name: 'watchlist_delete_film', methods: ['POST'])]
    public function deleteFilmFromWatchList(Film $film, EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        $user = $this->getUser();
        $watchList = $entityManager->getRepository(WatchList::class)->findOneBy([
            'watchListUser' => $user,
        ]);

        if ($watchList && $watchList->getFilm()->contains($film)) {
            $watchList->removeFilm($film);
            $entityManager->flush();
        }

        return $this->redirectToRoute('watchlist_index');
    }

    #[Route('/watchlist/delete-series/{id}', name: 'watchlist_delete_series', methods: ['POST'])]
    public function deleteSeriesFromWatchList(Series $series, EntityManagerInterface $entityManager): RedirectResponse
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $watchList = $entityManager->getRepository(WatchList::class)->findOneBy([
            'watchListUser' => $user,
        ]);

        if ($watchList && $watchList->getSeries()->contains($series)) {
            $watchList->removeSeries($series);
            $entityManager->flush();
        }

        return $this->redirectToRoute('watchlist_index');
    }


    #[Route('/watchlist/add-film/{id}', name: 'watchlist_add_film')]
    public function addFilmToWatchList(Film $film, EntityManagerInterface $entityManager): RedirectResponse
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $watchList = $entityManager->getRepository(WatchList::class)->findOneBy(['watchListUser' => $user]);

        if (!$watchList) {
            $watchList = new WatchList();
            $watchList->setWatchListUser($user);
            $entityManager->persist($watchList);
        }

        $watchList->addFilm($film);
        $entityManager->flush();

        return $this->redirectToRoute('app_film');
    }

    #[Route('/watchlist/add-series/{id}', name: 'watchlist_add_series')]
    public function addSeriesToWatchList(Series $series, EntityManagerInterface $entityManager): RedirectResponse
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $watchList = $entityManager->getRepository(WatchList::class)->findOneBy(['watchListUser' => $user]);

        if (!$watchList) {
            $watchList = new WatchList();
            $watchList->setWatchListUser($user);
            $entityManager->persist($watchList);
        }

        $watchList->addSeries($series);
        $entityManager->flush();

        return $this->redirectToRoute('app_series');
    }
}
