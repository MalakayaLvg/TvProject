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
    #[Route('/moderator/comment', name: 'app_comment_moderator', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/comment/create/film/{id}', name: 'app_comment_create_film', methods: ['GET', 'POST'])]
    public function createCommentOnFilm(Film $film,Request $request, EntityManagerInterface $entityManager): Response
    {

        if(!$this->getUser()){return $this->redirectToRoute('app_login');}


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
            "name"=>$film->getTitle(),
             /* IMAGE HERE*/
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/comment/create/series/{id}', name: 'app_comment_create_series', methods: ['GET', 'POST'])]
    public function createCommentOnSeries(Series $series,Request $request, EntityManagerInterface $entityManager): Response
    {

        if(!$this->getUser()){return $this->redirectToRoute('app_login');}


        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $content = $comment->getContent();

            if (empty($content)) {
                $this->addFlash('error', 'Le commentaire ne peut pas être vide.');
                return $this->redirectToRoute('app_comment_create', ['film_id' => $comment->getId()]);
            }
            $user = $this->getUser();
            $comment->setUserComment($user);
            $comment->setSeries($series);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('app_series_show',['id' => $series->getId()]);
        }

        return $this->render('client/comment/create.html.twig', [
            'name' => $series->getTitle(),
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/moderator/comment/show/{id}', name: 'app_comment_show_moderator', methods: ['GET'])]
    public function showAdmin(Comment $comment): Response
    {
        return $this->render('/admin/comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/moderator/comment/delete/{id}', name: 'app_comment_delete')]
    public function delete(Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('app_comment_moderator');
    }


}
