<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserListController extends AbstractController
{
    #[Route('/users', name: 'app_users_list')]
    public function list(UserRepository $userRepository): Response
    {
        return $this->render('user/list.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/users/delete/{id}', name: 'admin_user_delete')]
    public function delete(UserRepository $userRepository, EntityManagerInterface $entityManager, int $id): RedirectResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('The user not found');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'User deleted successfully!');
        return $this->redirectToRoute('app_users_list');
    }

}