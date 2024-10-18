<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\FilmRepository;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends AbstractController
{
    #[Route('/admin/image/add/{type}/{id}', name: 'add_image')]
    public function addImage($type, $id, Request $request, EntityManagerInterface $manager, FilmRepository $filmRepository, SeriesRepository $seriesRepository): Response
    {
        $image = new Image();
        $formImage = $this->createForm(ImageType::class, $image);
        $formImage->handleRequest($request);

        if ($formImage->isSubmitted() && $formImage->isValid()) {
            if ($type === 'film') {
                $film = $filmRepository->find($id);
                if ($film) {
                    $image->setFilm($film);
                }
            } elseif ($type === 'series') {
                $series = $seriesRepository->find($id);
                if ($series) {
                    $image->setSeries($series);
                }
            }

            $manager->persist($image);
            $manager->flush();

            if ($type === 'film') {
                return $this->redirectToRoute("app_admin_film_show", ["id" => $film->getId()]);
            } elseif ($type === 'series') {
                return $this->redirectToRoute("app_series_show_admin", ["id" => $series->getId()]);
            }
        }

        if ($type === 'film') {
            return $this->render('admin/film/image.html.twig', [
                'formImage' => $formImage->createView(),
            ]);

        }
        elseif ($type === 'series') {
            return $this->render('admin/series/image.html.twig', [
                'formImage' => $formImage->createView(),
            ]);
        }
    }


    #[Route('/admin/delete/image/{type}/{id}', name: 'delete_image')]
    public function delete(EntityManagerInterface $manager, Image $image)
    {
        if ($type === 'film') {
            $film = $image->getFilm();
            $manager->remove($image);
            $manager->flush();
            return $this->redirectToRoute("app_admin_film_show", ["id"=>$film->getId()]
            );

        }
        if ($type === 'series') {
            $series = $image->getSeries();
            $manager->remove($image);
            $manager->flush();
            return $this->redirectToRoute("app_series_admin_show", ["id"=>$series->getId()]
            );

        }


    }
}
