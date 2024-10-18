<?php

namespace App\Controller;

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Film;
use App\Entity\Series;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CommentController extends AbstractController
{
    #[Route('/admin/comment', name: 'app_comment_admin', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/comment/create/film/{id}', name: 'app_comment_create_film', methods: ['GET', 'POST'])]
    public function createCommentOnFilm(Film $film,Request $request, EntityManagerInterface $entityManager): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $content = $comment->getContent();

            if (empty($content)) {
                $this->addFlash('error', 'Le commentaire ne peut pas être vide.');
                return $this->redirectToRoute('app_comment_create', ['film_id' => $film->getId()]);
            }
            $user = $this->getUser();
            $comment->setUserComment($user);
            $comment->setFilm($film);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('app_film_show',['id' => $film->getId()]);
        }

        return $this->render('client/comment/create.html.twig', [
            'comment' => $comment,
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/comment/create/series/{id}', name: 'app_comment_create_series', methods: ['GET', 'POST'])]
    public function createCommentOnSeries(Series $series,Request $request, EntityManagerInterface $entityManager): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $content = $comment->getContent();

            if (empty($content)) {
                $this->addFlash('error', 'Le commentaire ne peut pas être vide.');
                return $this->redirectToRoute('app_comment_create', ['film_id' => $film->getId()]);
            }
            $user = $this->getUser();
            $comment->setUserComment($user);
            $comment->setSeries($series);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('app_film_show',['id' => $series->getId()]);
        }

        return $this->render('client/comment/create.html.twig', [
            'comment' => $comment,
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/admin/comment/show/{id}', name: 'app_comment_show_admin', methods: ['GET'])]
    public function showAdmin(Comment $comment): Response
    {
        return $this->render('/admin/comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/admin/comment/delete/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('app_comment_admin');
    }


}
