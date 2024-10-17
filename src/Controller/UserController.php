<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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
            "users" => $users,

        ]);
    }

    #[Route('/admin/user/show/{id}', name: 'app_user_admin_show')]
    public function show(User $user): Response
    {
        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_user_admin_delete', ['id' => $user->getId()]))
            ->setMethod('POST')
            ->getForm();

        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ]);
    }


    #[Route('/admin/user/edit/{id}', name: 'app_user_admin_edit', methods: ['GET', 'POST'])]
    public function editRole(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $currentRole = $user->getRoles()[0] ?? 'ROLE_USER';

        $form = $this->createFormBuilder()
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Moderator' => 'ROLE_MODERATOR',
                    'User' => 'ROLE_USER',
                ],
                'data' => $currentRole,
                'multiple' => false,
                'expanded' => false,
                'label' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Confirmer'])
            ->getForm();

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $selectedRole = $form->get('role')->getData();
            $user->setRoles([$selectedRole]);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_admin_show', ['id' => $user->getId()]);
        }

        return $this->render('admin/user/edit_role.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }


    #[Route('/admin/user/delete/{id}', name: 'app_user_admin_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            // Supprimer l'utilisateur
            $entityManager->remove($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_user_admin');
        }

        throw $this->createAccessDeniedException('Action non autorisée');
    }




}
