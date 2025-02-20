<?php

namespace App\Controller;

use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class UserProfileController extends AbstractController
{
    #[Route('/profile_edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('name')->getData();
            $user->setName($name);
            $email = $form->get('email')->getData();
            $user->setEmail($email);
            $password = $form->get('password')->getData();
            if ($password) {
                $user->setPassword($password);
            }

            $profilePictureFile = $form->get('profilePicture')->getData();
            if ($profilePictureFile) {
                $profilePictureFileName = md5(uniqid()) . '.' . $profilePictureFile->guessExtension();

                try {
                    $profilePictureFile->move(
                        $this->getParameter('profile_image_directory'),
                        $profilePictureFileName
                    );
                    $user->setProfilePicture($profilePictureFileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Upload image file failed!');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully!');
            return $this->redirectToRoute('app_profile_edit');
        }

        return $this->render('user/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

}