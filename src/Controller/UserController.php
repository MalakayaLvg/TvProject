<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/admin/users', name: 'app_user_admin')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/user/index.html.twig', [
            'controller_name' => 'UserController',
            "users" => $users
        ]);
    }

    #[Route('/admin/user/show/{id}', name: 'app_user_admin_show')]
    public function show(User $user): Response
    {

        return $this->render('admin/user/show.html.twig', [
            "user" => $user
        ]);
    }
}
