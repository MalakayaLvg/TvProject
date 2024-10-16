<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, EntityManagerInterface $manager): Response
    {
        // Créer le formulaire de recherche
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        // Initialiser les résultats à vide
        $films = [];

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le mot-clé de recherche
            $searchTerm = $form->get('query')->getData();

            // Rechercher dans la base de données
            $films = $manager->getRepository(Film::class)
                ->createQueryBuilder('p')
                ->where('p.title LIKE :searchTerm OR p.description LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%')
                ->getQuery()
                ->getResult();

        }

        return $this->render('search/index.html.twig', [
            'form' => $form->createView(),
            'films' => $films,
        ]);
    }

}
