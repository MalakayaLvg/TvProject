<?php

namespace App\Controller;

use App\Entity\WatchList;
use App\Repository\WatchListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Film;
use App\Entity\Series;
use Symfony\Component\HttpFoundation\RedirectResponse;


class WatchListController extends AbstractController
{
    #[Route('/profile/watchlist', name: 'app_profile_watchlist')]
    public function index(EntityManagerInterface $entityManager): \Symfony\Component\HttpFoundation\Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $watchList = $user->getWatchList();

        if (!$watchList) {
            $watchList = new WatchList();
            $watchList->setOwner($user);
            $entityManager->persist($watchList);
            $entityManager->flush();
        }
        return $this->render('/client/user/profile.html.twig', [
            'watchList' => $watchList,
        ]);
    }

    #[Route('/watchlist/delete-film/{id}', name: 'watchlist_delete_film')]
    public function deleteFilmFromWatchList(Film $film, EntityManagerInterface $entityManager, WatchListRepository $watchListRepository): RedirectResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        $user = $this->getUser();
        $watchList = $watchListRepository->findOneBy([
            'owner' => $user,
        ]);

        if ($watchList && $watchList->getFilms()->contains($film)) {
            $watchList->removeFilm($film);
            $entityManager->persist($watchList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_profile_watchlist');
    }

    #[Route('/watchlist/delete-series/{id}', name: 'watchlist_delete_series')]
    public function deleteSeriesFromWatchList(Series $series, EntityManagerInterface $entityManager, WatchListRepository $watchListRepository): RedirectResponse
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $watchList =$watchListRepository->findOneBy([
            'owner' => $user,
        ]);

        if ($watchList && $watchList->getSeries()->contains($series)) {
            $watchList->removeSeries($series);
            $entityManager->persist($watchList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_profile_watchlist');
    }


    #[Route('/watchlist/add-film/{id}', name: 'watchlist_add_film')]
    public function addFilmToWatchList(Film $film, EntityManagerInterface $entityManager): RedirectResponse
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $watchList = $user->getWatchList();

        if (!$watchList) {
            $watchList = new WatchList();
            $watchList->setOwner($user);
            $entityManager->persist($watchList);
            $entityManager->flush();
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

        $watchList = $user->getWatchList();

        if (!$watchList) {
            $watchList = new WatchList();
            $watchList->setOwner($user);
            $entityManager->persist($watchList);
            $entityManager->flush();
        }

        $watchList->addSeries($series);
        $entityManager->flush();

        return $this->redirectToRoute('app_series');
    }
}