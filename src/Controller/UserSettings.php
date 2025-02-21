<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserSettings extends AbstractController
{
    #[Route('/admin/user/edit/{id}', name: 'admin_user_edit')]
    public function editUser(int $id, EntityManagerInterface $entityManager, Request $request, Security $security): Response
    {
        if (!$security->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You are not allowed to access this page');
        }

        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('The user not found');
        }

        if ($request->isMethod('POST')) {
            $user->setName($request->request->get('name'));
            $user->setEmail($request->request->get('email'));
            $roles = $request->request->get('roles');
            $user->setRoles(explode(',', $roles));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'User updated successfully!');
            return $this->redirectToRoute('admin_user_edit', ['id' => $id]);
        }

        return $this->render('admin/user_edit.html.twig', [
            'user' => $user
        ]);
    }

}