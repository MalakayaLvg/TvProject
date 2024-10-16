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
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
//
        $films = [];

        if($form->isSubmitted() && $form->isValid())
        {
            $searchTerm = $form->get('query')->getData();

            $queryBuilder = $manager->createQueryBuilder();

            $films = $queryBuilder
                ->select('f')
                ->from(Film::class, 'f')
                ->where($queryBuilder->expr()->like('f.title', ':title'))
                ->setParameter('title', '%' . $searchTerm . '%');

                return $queryBuilder->getQuery()->getResult();

        }
        return $this->render('search/index.html.twig',[
            'form' => $form->createView(),
            'films' => $films,
        ]);
    }
}
