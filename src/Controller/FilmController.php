<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\FilmType;
use App\Form\SearchType;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FilmController extends AbstractController
{
    #[Route('/film', name: 'app_film')]
    public function index(FilmRepository $filmRepository, Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        // Initialiser les résultats à vide
        $films = [];
        $form = $this->createForm(SearchType::class,null,["method"=>'GET']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchTerm = $form->get('query')->getData();

            $films = $manager->getRepository(Film::class)
                ->createQueryBuilder('p')
                ->where('p.title LIKE :searchTerm OR p.description LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%')
                ->getQuery()
                ->getResult();
            return $this->render('/client/home/index.html.twig', [
                "films" => $filmRepository->findAll(),
                "search"=> true,
                'form' => $form->createView(),
            ]);

        }
        // Si le formulaire est soumis et valide
        $boxOfficeFilms = $filmRepository->findBy(['budget' => ['gte' => 1000000]], ['critical_rate' => 'DESC'], 10);
        $recommendedFilms = $filmRepository->findBy([], ['publish_date' => 'DESC'], 10);

        return $this->render('/client/home/index.html.twig', [
            "films" => $boxOfficeFilms,
            "filmsForYou" => $recommendedFilms,
            "bestRated" => [],
            'form' => $form->createView(),
        ]);
    }
    #[Route('/admin/film', name: 'app_film_admin')]
    public function indexAdmin(FilmRepository $filmRepository): Response
    {



        return $this->render('/admin/film/index.html.twig',[
            "films" => $filmRepository->findAll(),
        ]);
    }
    #[Route('/admin/film/create', name: 'app_film_create')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_film');
        }

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

    #[Route("/admin/new/delete/{id}", name:'app_film_delete')]
    public function delete(Film $film, EntityManagerInterface $manager):Response
    {
        if (!$this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_film');
        }

        $manager->remove($film);
        $manager->flush();

        return $this->redirectToRoute("app_film");
    }

    #[Route('/admin/film/edit/{id}', name: 'app_film_edit')]
    public function edit(Film $film,Request $request, EntityManagerInterface $manager): Response
    {  if (!$this->isGranted('ROLE_ADMIN')){
        return $this->redirectToRoute('app_film');
    }
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
        return $this->render("/admin/film/show.html.twig",[
            "film" => $film
        ]);
    }
}
