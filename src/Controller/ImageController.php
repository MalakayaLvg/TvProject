<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends AbstractController
{
    #[Route('/image/add/film/{id}', name: 'add_film_image')]
    public function index($id, Request $request, EntityManagerInterface $manager, FilmRepository $filmRepository): Response
    {
        $image = new Image();
        $formImage = $this->createForm(ImageType::class, $image);
        $formImage->handleRequest($request);
        if($formImage->isSubmitted() && $formImage->isValid())
        {
            $image->setFilm($manager->getRepository(Film::class)->find($id));
            $manager->persist($image);
            $manager->flush();
        }
        return $this->redirectToRoute("app_admin_film_show", ["id"=>$image->getFilm()->getId()]);


    }

    #[Route('/delete/image/{id}', name: 'delete_film_image')]
    public function delete(EntityManagerInterface $manager, Image $image)
    {
        $film = $image->getFilm();
        $manager->remove($image);
        $manager->flush();
        return $this->redirectToRoute("app_admin_film_show", ["id"=>$film->getId()]
        );
    }
}
