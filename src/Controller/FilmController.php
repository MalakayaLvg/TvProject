<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\FilmType;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FilmController extends AbstractController
{
    #[Route('/film', name: 'app_film')]
    public function index(FilmRepository $filmRepository): Response
    {



        return $this->render('/admin/film/index.html.twig',[
            "films" => $filmRepository->findAll(),
        ]);
    }

    #[Route('/film/create', name: 'app_film_create')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $film = new Film();
        $form = $this->createForm(FilmType::class,$film);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){;
            $manager->persist($film);
            $manager->flush();
            return $this->redirectToRoute('app_film');
        }

        return $this->render("/admin/film/create.html.twig",[
            'form' => $form->createView()
        ]);
    }

    #[Route("/new/delete/{id}", name:'app_film_delete')]
    public function delete(Film $film, EntityManagerInterface $manager):Response
    {

        $manager->remove($film);
        $manager->flush();

        return $this->redirectToRoute("app_film");
    }

    #[Route('/film/edit/{id}', name: 'app_film_edit')]
    public function edit(Film $film,Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(FilmType::class,$film);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){;
            $manager->persist($film);
            $manager->flush();
            return $this->redirectToRoute('app_film');
        }

        return $this->render("/admin/film/create.html.twig",[
            'form' => $form->createView(),
            'film' => $film
        ]);
    }

    #[Route('/film/show/{id}', name: 'app_film_show')]
    public function show(Film $film):Response
    {
        return $this->render("film/show.html.twig",[
            "film" => $film
        ]);
    }
}
